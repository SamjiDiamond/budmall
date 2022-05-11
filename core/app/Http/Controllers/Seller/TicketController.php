<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Traits\SupportTicketManager;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use SupportTicketManager;
    protected $seller;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->seller = seller();
            return $next($request);
        });
    }

    public function index()
    {
        $pageTitle      = "All Support Tickets";
        $emptyMessage   = 'No support ticket has opened yet';
        $tickets        = SupportTicket::where('seller_id', $this->seller->id)
                            ->orderBy('priority', 'desc')
                            ->orderBy('id','desc')
                            ->paginate(getPaginate());
        return view('seller.support.index', compact('tickets', 'pageTitle', 'emptyMessage'));
    }

    public function viewTicket($ticket)
    {
        $pageTitle  = "View Ticket";
        $myTicket   = SupportTicket::where('ticket', $ticket)->where('seller_id', $this->seller->id)->firstOrFail();
        $messages   = SupportMessage::where('supportticket_id', $myTicket->id)->orderBy('id','desc')->get();
        return view('seller.support.view', compact('myTicket', 'messages', 'pageTitle'));
    }

    public function openNewTicket()
    {
        $pageTitle  = "Open New Ticket";
        return view('seller.support.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->merge(['email'=>$this->seller->email, 'name'=>$this->seller->fullname]);
        $ticket = $this->storeTicket($request, $this->seller->id, 'seller');
        return redirect()->route('seller.ticket.index')->withNotify($ticket['message']);
    }

    public function reply(Request $request, $id)
    {
        $reply = $this->replyTicket($request, $id, 'seller');
        return redirect()->route('seller.ticket.view', $reply['ticket'])->withNotify($reply['message']);
    }

}
