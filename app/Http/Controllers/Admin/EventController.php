<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Category;
use App\Models\Event;
use App\Models\TransactionDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::paginate(10);

        return view('admin.event.index', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('admin.event.form', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        // Create a slug
        $request->merge([
            'slug' => Str::slug($request->name)
        ]);

        // Handle is popular checkbox
        $request->merge([
            'is_popular' => $request->has('is_popular')
        ]);

        // Upload multiple photos
        if ($request->hasFile('files')) {
            $photos = [];

            foreach ($request->file('files') as $file) {
                $photos[] = $file->store('events', 'public');
            }

            $request->merge([
                'photos' => $photos
            ]);
        }

        // Create event
        Event::create($request->except('files'));

        // Return to index
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil ditambahkan');
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
    public function edit(Event $event)
    {
        $categories = Category::all();

        return view('admin.event.form', [
            'categories' => $categories,
            'event' => $event
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, string $id)
    {
        // Create a slug
        $request->merge([
            'slug' => Str::slug($request->name)
        ]);

        // Handle is popular checkbox
        $request->merge([
            'is_popular' => $request->has('is_popular')
        ]);

        // Upload multiple photos
        if ($request->hasFile('files')) {
            $photos = [];

            foreach ($request->file('files') as $file) {
                $photos[] = $file->store('events', 'public');
            }

            $request->merge([
                'photos' => $photos
            ]);
        }

        // Update event
        Event::find($id)->update($request->except('files'));

        // Return to index
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus');
    }

    public function scan(Event $event)
    {
        return view('admin.event.scan', [
            'event' => $event
        ]);
    }

    public function scanAPI(Event $event)
    {
        request()->validate([
            'code' => 'required|exists:transaction_details,code'
        ]);

        $transactionDetail = TransactionDetail::where('code', request()->code)->first();

        if ($transactionDetail) {
            $transactionDetail->update([
                'is_redeemed' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check in success'
            ]);
        }
    }
}
