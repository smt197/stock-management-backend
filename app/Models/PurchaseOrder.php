<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'reference',
        'supplier_id',
        'user_id',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'status',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the supplier that owns the purchase order.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who created the purchase order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the purchase order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'partially_received' => 'Partiellement reçue',
            'received' => 'Reçue',
            'cancelled' => 'Annulée',
            default => $this->status,
        };
    }

    /**
     * Check if order is fully received.
     */
    public function isFullyReceived(): bool
    {
        return $this->items()->get()->every(function ($item) {
            return $item->quantity_received >= $item->quantity_ordered;
        });
    }

    /**
     * Check if order is partially received.
     */
    public function isPartiallyReceived(): bool
    {
        $hasReceived = $this->items()->where('quantity_received', '>', 0)->exists();
        $notFullyReceived = !$this->isFullyReceived();
        return $hasReceived && $notFullyReceived;
    }
}
