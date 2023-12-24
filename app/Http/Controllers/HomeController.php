<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $events = $this->fetchEvents();
        $categories = $this->fetchCategories();

        return view('frontend.index', compact('events', 'categories'));
    }

    private function fetchEvents()
    {
        $category = request()->query('category');

        $events = Event::upcoming();

        if (!request()->query('all_events')) {
            $events->limit(6);
        }

        if ($category) {
            $events->withCategory($category);
        }

        return $events->get();

    }

    private function fetchCategories()
    {
        $categories = Category::sortByMostEvents();

        if (!request()->query('all_categories')) {
            $categories->limit(4);
        }

        return $categories->get();
    }
}
