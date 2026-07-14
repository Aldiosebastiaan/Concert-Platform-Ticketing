<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with(['kategori', 'tikets'])
            ->orderBy('tanggal_waktu', 'asc')
            ->get()
            ->map(function ($event) {
                // Attach the image URL accessor manually for JSON representation
                $event->gambar_url = $event->image_url;
                return $event;
            });

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function show($id)
    {
        $event = Event::with(['kategori', 'tikets'])->findOrFail($id);
        $event->gambar_url = $event->image_url;
        
        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }
}
