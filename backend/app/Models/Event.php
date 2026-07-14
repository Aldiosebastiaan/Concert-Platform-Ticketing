<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_kategori',
        'judul',
        'deskripsi',
        'lokasi',
        'tanggal_waktu',
        'gambar',
    ];

    public function tikets()
    {
        return $this->hasMany(Ticket::class, 'id_event');
    }

    public function kategori()
    {
        return $this->belongsTo(Category::class, 'id_kategori');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_event');
    }

    public function getStatusAttribute()
    {
        $now = now();
        $eventTime = Carbon::parse($this->tanggal_waktu);

        if ($eventTime->isFuture()) {
            return 'Upcoming';
        }

        if ($now->between($eventTime, $eventTime->copy()->addHours(3))) {
            return 'Ongoing';
        }

        return 'Completed';
    }

    public function hasSales()
    {
        return $this->orders()->exists();
    }
    public function scopeUpcoming($query)
    {
        return $query->where('tanggal_waktu', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('tanggal_waktu', '<=', now())
                     ->where('tanggal_waktu', '>=', now()->subHours(3));
    }

    public function scopeCompleted($query)
    {
        return $query->where('tanggal_waktu', '<', now()->subHours(3));
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->gambar)) {
            return 'konser.jpg';
        }

        if (filter_var($this->gambar, FILTER_VALIDATE_URL)) {
            return $this->gambar;
        }

        if (Storage::disk('public')->exists($this->gambar)) {
            return Storage::url($this->gambar);
        }

        return 'konser.jpg';
    }
}
