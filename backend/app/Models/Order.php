<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['id_user', 'id_event', 'order_date', 'total_price'];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event');
    }

    public function detailOrders(): HasMany
    {
        return $this->hasMany(DetailOrder::class, 'id_order');
    }
}
