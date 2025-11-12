<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'cost_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relation avec la vente
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relation avec le produit
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculer le profit par item
     */
    public function getProfitAttribute(): float
    {
        return ($this->unit_price - $this->cost_price) * $this->quantity;
    }

    /**
     * Calculer le pourcentage de marge
     */
    public function getMarginPercentageAttribute(): float
    {
        if ($this->cost_price == 0) {
            return 0;
        }

        return (($this->unit_price - $this->cost_price) / $this->cost_price) * 100;
    }
}
