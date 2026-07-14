<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailOrder extends Model
{
    use HasFactory;

    protected $fillable = ['id_order', 'id_tiket', 'jumlah', 'subtotal'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'id_tiket');
    }
}
