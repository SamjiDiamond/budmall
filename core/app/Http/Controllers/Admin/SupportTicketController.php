<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Traits\SupportTicketManager;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    use SupportTicketManager;

    protected $view;

    public function tickets()
    {
        $pageTitle      = 'Support Tickets';
        $emptyMessage   = 'No Data found.';
        $tickets        = $this->filterTickets([0,1,2,3], request()->search);
        return view('admin.support.tickets', compact('tickets', 'pageTitle','emptyMessage'));
    }


    protected function filterTickets($status,$searchKey)
    {
        $query = SupportTicket::whereIn('status', $status);

        if($searchKey){
            $query = $query->where(function($q) use($searchKey){
                $q->where('name', 'LIKE', "%$searchKey%")->orWhere('ticket', 'LIKE', "%$searchKey%")->orWhere('subject', 'LIKE', "%$searchKey%");
            });
        }

        $tickets = $query->orderBy('priority', 'DESC')->orderBy('id','desc')->with(['user','seller'])->paginate(getPaginate());

        return $tickets;
    }

    public function pendingTicket()
    {
        $pageTitle      = 'Pending Tickets';
        $emptyMessage   = 'No Data found.';
        $tickets        = $this->filterTickets([0,2], request()->search);
        return view('admin.support.tickets', compact('tickets', 'pageTitle','emptyMessage'));
    }

    public function closedTicket()
    {
        $emptyMessage   = 'No Data found.';
        $pageTitle      = 'Closed Tickets';
        $tickets        = $this->filterTickets([3], request()->search);
        return view('admin.support.tickets', compact('tickets', 'pageTitle','emptyMessage'));
    }

    public function answeredTicket()
    {
        $pageTitle      = 'Answered Tickets';
        $emptyMessage   = 'No Data found.';
        $tickets        = $this->filterTickets([1], request()->search);
        return view('admin.support.tickets', compact('tickets', 'pageTitle','emptyMessage'));
    }


    public function ticketReply($id)
    {
        $ticket = SupportTicket::with('user')->where('id', $id)->firstOrFail();
        $pageTitle = 'Reply Ticket';
        $messages = SupportMessage::with('ticket')->where('supportticket_id', $ticket->id)->orderBy('id','desc')->get();
        return view('admin.support.reply', compact('ticket', 'messages', 'pageTitle'));
    }


    public function reply(Request $request, $id)
    {
        $reply = $this->replyTicket($request, $id, 'admin');

        return redirect()->route('admin.ticket.view', $request->id)->withNotify($reply);
    }

    public function ticketDelete(Request $request)
    {
        $message = SupportMessage::findOrFail($request->message_id);
        $path = imagePath()['ticket']['path'];
        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $attachment) {
                removeFile($path.'/'.$attachment->attachment);
                $attachment->delete();
            }
        }
        $message->delete();
        $notify[] = ['success', "Delete successfully"];
        return back()->withNotify($notify);
    }



}
