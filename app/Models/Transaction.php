<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'event_id',
        'name',
        'email',
        'status',
        'fee_price',
        'unique_price',
        'total_price',
        'payment_method',
    ];

    // Relation to event

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Relation to transaction details

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
