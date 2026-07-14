<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::with(['kategori', 'tikets']);

        // Filter by kategori_id
        if ($request->filled('kategori_id')) {
            $query->where('id_kategori', $request->kategori_id);
        }

        // Search by judul or lokasi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        // Sort by tanggal_waktu
        $sort = $request->get('sort', 'asc');
        $query->orderBy('tanggal_waktu', $sort);

        // Paginate
        $events = $query->paginate(10);

        return view('pages.admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('pages.admin.events.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventFormRequest $request)
    {
        $validated = $request->validated();

        $gambar = 'konser.jpg';
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store('events', 'public');
        }

        $event = Event::create([
            'id_user' => auth()->id(),
            'id_kategori' => $validated['kategori_id'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'lokasi' => $validated['lokasi'],
            'tanggal_waktu' => $validated['tanggal_waktu'],
            'gambar' => $gambar,
        ]);

        if (isset($validated['tikets']) && is_array($validated['tikets'])) {
            foreach ($validated['tikets'] as $tiket) {
                $event->tikets()->create([
                    'tipe' => $tiket['tipe'],
                    'harga' => $tiket['harga'],
                    'stok' => $tiket['stok'],
                ]);
            }
        }

        return redirect()->route('admin.events.index')
                         ->with('success', 'Event berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load(['kategori', 'tikets']);

        $relatedEvents = Event::where('id_kategori', $event->id_kategori)
            ->where('id', '!=', $event->id)
            ->where('tanggal_waktu', '>', now())
            ->take(4)
            ->get();

        return view('pages.events.show', compact('event', 'relatedEvents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $event->load(['kategori', 'tikets']);
        $categories = Category::all();
        $hasSales = $event->hasSales();

        return view('pages.admin.events.edit', compact('event', 'categories', 'hasSales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventFormRequest $request, Event $event)
    {
        $validated = $request->validated();
        $hasSales = $event->hasSales();

        if ($hasSales && Carbon::parse($event->tanggal_waktu)->ne(Carbon::parse($validated['tanggal_waktu']))) {
            return back()->withErrors(['tanggal_waktu' => 'Tidak dapat mengubah tanggal/waktu event karena tiket sudah ada yang terjual.'])->withInput();
        }

        $gambar = $event->gambar;
        if ($request->hasFile('gambar')) {
            if ($gambar && $gambar !== 'konser.jpg' && Storage::disk('public')->exists($gambar)) {
                Storage::disk('public')->delete($gambar);
            }
            $gambar = $request->file('gambar')->store('events', 'public');
        }

        $event->update([
            'id_kategori' => $validated['kategori_id'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'lokasi' => $validated['lokasi'],
            'tanggal_waktu' => $validated['tanggal_waktu'],
            'gambar' => $gambar,
        ]);

        if (isset($validated['tikets']) && is_array($validated['tikets'])) {
            $submittedTicketIds = collect($validated['tikets'])->pluck('id')->filter()->all();
            
            // Delete removed tickets (hanya jika belum ada penjualan)
            if (!$hasSales) {
                $event->tikets()->whereNotIn('id', $submittedTicketIds)->delete();
            }

            foreach ($validated['tikets'] as $tiketData) {
                if (isset($tiketData['id']) && $tiketData['id']) {
                    // Update existing
                    $event->tikets()->where('id', $tiketData['id'])->update([
                        'tipe' => $tiketData['tipe'],
                        'harga' => $tiketData['harga'],
                        'stok' => $tiketData['stok'],
                    ]);
                } else {
                    // Create new
                    $event->tikets()->create([
                        'tipe' => $tiketData['tipe'],
                        'harga' => $tiketData['harga'],
                        'stok' => $tiketData['stok'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.events.index')
                         ->with('success', 'Event berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        if ($event->hasSales()) {
            return back()->with('error', 'Tidak dapat menghapus event yang sudah memiliki penjualan tiket.');
        }

        if ($event->gambar && $event->gambar !== 'konser.jpg' && Storage::disk('public')->exists($event->gambar)) {
            Storage::disk('public')->delete($event->gambar);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
                         ->with('success', 'Event berhasil dihapus.');
    }
}