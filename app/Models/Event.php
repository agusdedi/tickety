<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'headline',
        'description',
        'start_time',
        'location',
        'duration',
        'is_popular',
        'photos',
        'type',
        'category_id',
    ];

    protected $casts = [
        'photos' => 'array',
        'start_time' => 'datetime',
    ];

    // Relation to tickets

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Relation to category

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Get the lowest price ticket for the event
    
    public function getStartFromAttribute()
    {
        return $this->tickets()->orderBy('price')->first()->price ?? 0;
    }

    // Get the first photos at the thumbnail from the photos attribute, if not exist return default image

    public function getThumbnailAttribute()
    {
        $photos = $this->photos;

        if($photos && !empty($photos)) {
            return Storage::url($photos[0]);
        }

        return asset('assets/images/event-1.webp');
    }

    // Scope a query to only include events with certain category

    public function scopeWithCategory($query, $category)
    {
        return $query->where('category_id', $category);
    }

    // Scope a query to only include upcoming events

    public function scopeUpcoming($query)
    {
        return $query->orderBy('start_time', 'asc')->where('start_time', '>=', now());
    }

    // Scope a query to find event by slug

    public function scopeFetch($query, $slug)
    {
        // Event::fetch($slug)
        return $query->with(['tickets', 'category'])
            ->withCount('tickets')
            ->where('slug', $slug)
            ->firstOrFail();
    }
}
