<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'icon'
    ];

    // Relation to Event

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // Scope a query to sort by the number of events

    public function scopeSortByMostEvents($query)
    {
        return $query->withCount('events')->orderBy('events_count', 'desc');
    }
}
