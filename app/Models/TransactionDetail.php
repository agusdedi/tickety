<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'ticket_id',
        'transaction_id',
        'is_redeemed',
    ];

    // Relation to ticket

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relation to transaction

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
