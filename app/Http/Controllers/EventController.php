<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request, $slug)
    {
        $event = Event::fetch($slug);

        return view('frontend.details', compact('event'));
    }

    public function checkout(Request $request, $slug)
    {
        $event = Event::fetch($slug);

        $selectedTickets = collect($request->tickets)->filter(function($quantity){
            return $quantity > 0;
        });

        $tickets = $selectedTickets->map(function($quantity, $id) {
            $ticket = Ticket::find($id);

            return $ticket ? (object) [
                'name' => $ticket->name,
                'quantity' => (int) $quantity,
                'price' => $ticket->price,
                'total' => $ticket->price * $quantity,
            ] : null;
        })->filter();

        $uniquePrice = rand(1,10);
        $tax = 0.22 * $tickets->sum('total');
        $totalPrice = $tickets->sum('total') + $uniquePrice + $tax;

        //Temporarily store to session
        $request->session()->put('event', $event);
        $request->session()->put('tickets', $tickets);
        $request->session()->put('uniquePrice', $uniquePrice);
        $request->session()->put('totalPrice', $totalPrice);

        return view('frontend.checkout', compact('event', 'tickets', 'uniquePrice', 'tax', 'totalPrice'));
    }

    public function checkoutPay(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'payment_method' => 'required|string|in:manual_transfer, virtual_account, credit_card, my_wallet'
        ]);

        $event = $request->session()->get('event');
        $tickets = $request->session()->get('tickets');
        $uniquePrice = $request->session()->get('uniquePrice');
        $totalPrice = $request->session()->get('totalPrice');

        $transaction = Transaction::create([
            'code' => 'TRX' . mt_rand(10000, 999999),
            'event_id' => $event->id,
            'name' => $request->name,
            'email' => $request->email,
            'status' =>'pending',
            'unique_price' => $uniquePrice,
            'total_price' => $totalPrice,
            'payment_method' => $request->payment_method,
        ]);
        
        foreach ($tickets as $ticket) {
            for ($i = 0; $i < $ticket->quantity; $i++) {
                $transaction->transactionDetails()->create([
                    'code' => 'TIX' . mt_rand(10000, 999999),
                    'ticket_id' => Ticket::where('name', $ticket->name)->first()->id,
                    'transaction_id' => $transaction->id,
                    'is_redeemed' => false,
                ]);
            }
        }

        return redirect()->route('checkout-success');
    }

    public function checkoutSuccess()
    {
        return view('frontend.checkout-success');
    }
}
