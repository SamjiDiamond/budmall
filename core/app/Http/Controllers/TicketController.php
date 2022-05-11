<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Traits\SupportTicketManager;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use SupportTicketManager;

    protected $user;
    public function __construct()
    {

        $this->activeTemplate = activeTemplate();

        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    public function index()
    {
        $pageTitle      = "All Support Tickets";
        $emptyMessage   = 'No support ticket has opened yet';
        $tickets        = SupportTicket::where('user_id', auth()->id())
                            ->orderBy('priority', 'desc')
                            ->orderBy('id','desc')
                            ->paginate(getPaginate());
        return view($this->activeTemplate.'user.support.index', compact('tickets', 'pageTitle', 'emptyMessage'));
    }

    public function openNewTicket()
    {
        $pageTitle  = "Open New Ticket";
        return view($this->activeTemplate . 'user.support.create', compact('pageTitle'));
    }


    public function store(Request $request)
    {
        $request->merge(['email'=>$this->user->email, 'name'=> $this->user->fullname]);
        $store = $this->storeTicket($request, $this->user->id, 'user');
        return redirect()->route('ticket')->withNotify($store['message']);
    }

    public function viewTicket($ticket)
    {
        $pageTitle  = "View Ticket";
        $myTicket   = SupportTicket::where('ticket', $ticket)->where('user_id', $this->user->id??null)->firstOrFail();
        $messages   = SupportMessage::where('supportticket_id', $myTicket->id)->orderBy('id','desc')->get();
        return view($this->activeTemplate .'user.support.view', compact('myTicket', 'messages', 'pageTitle'));
    }

    public function viewGuestTicket($ticket)
    {
        $pageTitle  = "View Contact Message";
        $myTicket   = SupportTicket::where('ticket', $ticket)->where('user_id', null)->firstOrFail();

        if($myTicket->user_id > 0){
            abort(403);
        }

        $messages   = SupportMessage::where('supportticket_id', $myTicket->id)->orderBy('id','desc')->get();

        return view($this->activeTemplate .'user.support.contact_message', compact('myTicket', 'messages', 'pageTitle'));
    }

    public function reply(Request $request, $ticket)
    {
        $reply = $this->replyTicket($request, $ticket, 'user');
        return redirect()->route('ticket.view', $reply['ticket'])->withNotify($reply['message']);
    }

}
