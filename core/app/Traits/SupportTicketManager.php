<?php

namespace App\Traits;

use App\Models\AdminNotification;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait SupportTicketManager {
    protected $files;
    protected $allowedExtension = ['jpg', 'png', 'jpeg', 'pdf', 'doc','docx'];


    public function storeTicket(Request $request, $userId, $userType)
    {
        // Validate form data

        $this->validation($request);
        $ticketNumber      = getNumber();
        $supportTicket     = new SupportTicket();
        $adminNotification = new AdminNotification();
        $supportMessage    = new SupportMessage();

        if($userType == 'user'){
            $supportTicket->user_id         = $userId;
            $adminNotification->user_id     = $userId;
        }

        if($userType == 'seller'){
            $supportTicket->seller_id       = $userId;
            $adminNotification->seller_id   = $userId;
        }

        $supportTicket->ticket              = $ticketNumber;
        $supportTicket->name                = $request->name;
        $supportTicket->email               = $request->email;
        $supportTicket->subject             = $request->subject;
        $supportTicket->last_reply          = Carbon::now();
        $supportTicket->status              = 0;
        $supportTicket->priority            = $request->priority;
        $supportTicket->save();

        $supportMessage->supportticket_id   = $supportTicket->id;
        $supportMessage->message            = $request->message;
        $supportMessage->save();

        $adminNotification->title           = 'New support ticket opened successfully';
        $adminNotification->click_url       = urlPath('admin.ticket.view',$supportTicket->id);
        $adminNotification->save();

        if($request->hasFile('attachments')) {
            $uploadAttachments = $this->storeSupportAttachments($supportMessage->id);

            if(!$uploadAttachments){
                $notify[] = ['error', 'Could not upload your file'];
                return $notify;
            }
        }

        $notify[] = ['success', 'Ticket created successfully'];

        return [
            'ticket' => $ticketNumber,
            'message' => $notify
        ];
    }


    public function replyTicket($request, $id, $type)
    {

        $ticket         = SupportTicket::where("id", $id)->with('user')->firstOrFail();
        $supportMessage = new SupportMessage();

        if($request->reply_ticket == 1) {

            $this->validation($request);
            if($type == 'admin'){
                $ticket->status             = 1;
                $supportMessage->admin_id   = auth()->guard('admin')->id();
            }else{
                $ticket->status             = 2;
            }
            $ticket->last_reply             = Carbon::now();
            $ticket->save();

            $supportMessage->supportticket_id   = $ticket->id;
            $supportMessage->message            = $request->message;
            $supportMessage->save();

            $notify[] = ['success', 'Your reply sent successfully'];

            if($request->hasFile('attachments')) {
                $uploadAttachments = $this->storeSupportAttachments($supportMessage->id);

                if(!$uploadAttachments){
                    $notify[] = ['error', 'Could not upload your file'];
                    return $notify;
                }
            }

            if($type == 'admin'){
                notify($ticket, 'ADMIN_SUPPORT_REPLY', [
                    'ticket_id' => $ticket->ticket,
                    'ticket_subject' => $ticket->subject,
                    'reply' => $request->message,
                    'link' => route('ticket.view.guest', $ticket->ticket),
                ]);
            }
        }elseif ($request->reply_ticket == 2) {
            $ticket->status     = 3;
            $ticket->last_reply = Carbon::now();
            $ticket->save();

            $notify[]           = ['success', 'Support ticket closed successfully'];
        }else{
            $notify[]           = ['error','Invalid request'];
        }

        return [
            'ticket' => $ticket->ticket,
            'message' => $notify
        ];
    }

    protected function validation($request){

        $this->files = $request->file('attachments');

        $request->validate([
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail){
                    foreach ($this->files as $file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (($file->getSize() / 1000000) > 2) {
                            return $fail("Maximum 2MB file size allowed!");
                        }
                        if (!in_array($ext,$this->allowedExtension)) {
                            return $fail("Only png, jpg, jpeg, pdf, doc, docx files are allowed");
                        }
                    }
                    if (count($this->files) > 5) {
                        return $fail("Maximum 5 files can be uploaded");
                    }
                },
            ],
            'name'      => 'required_without:reply_ticket',
            'email'     => 'required_without:reply_ticket|email|max:191',
            'subject'   => 'required_without:reply_ticket|max:100',
            'priority'  => 'required_without:reply_ticket|in:1,2,3',
            'message'   => 'required',
        ]);
    }

    protected function storeSupportAttachments($supportMessageId)
    {
        $path = imagePath()['ticket']['path'];

        foreach ($this->files as  $file) {
            try {
                $attachment                     = new SupportAttachment();
                $attachment->support_message_id = $supportMessageId;
                $attachment->attachment         = uploadFile($file, $path);
                $attachment->save();
            } catch (\Exception $exp) {
                return false;
            }
        }

        return true;
    }

    public function ticketDownload($ticket_id)
    {
        $attachment = SupportAttachment::findOrFail(decrypt($ticket_id));
        $file = $attachment->attachment;

        $path = imagePath()['ticket']['path'];
        $full_path = $path.'/'. $file;

        $title = slug($attachment->supportMessage->ticket->subject);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimetype = mime_content_type($full_path);

        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        header("Content-Type: " . $mimetype);
        return readfile($full_path);
    }
}
