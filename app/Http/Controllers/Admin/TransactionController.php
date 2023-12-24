<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Transaction;
use App\Mail\TicketMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        $transanctions = Transaction::with('transactionDetails.ticket')
            ->where('event_id', $event->id)
            ->paginate(10);

        return view('admin.event.transactions.index', [
            'event' => $event,
            'transactions' => $transanctions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Transaction $transaction)
    {
        $transaction->delete();

        return redirect()
            ->route('admin.events.transactions.index', $event->id)
            ->with('success', 'Transaksi berhasil dihapus');
    }

    public function pdf(Event $event, Transaction $transaction)
    {
        $pdf = Pdf::loadView('pdf.ticket', [
            'event' => $event,
            'transaction' => $transaction,
        ]);

        return $pdf->stream();
    }

    public function approve(Event $event, $id)
    {
        // Update transaction
        Transaction::find($id)->update([
            'status' => 'success',
        ]);

        $transaction = Transaction::find($id);

        // Send email with pdf
        Mail::to($transaction->email)->send(new TicketMail($event, $transaction));

        return redirect()
            ->route('admin.events.transactions.index', $event->id)
            ->route('success', 'Transaksi berhasil dikonfirmasi');
    }
}
