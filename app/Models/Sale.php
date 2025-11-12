<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'sale_number',
        'customer_name',
        'customer_phone',
        'total_amount',
        'payment_method',
        'payment_status',
        'amount_paid',
        'amount_due',
        'notes',
        'user_id',
        'sale_date',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    /**
     * Relation avec les items de la vente
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Relation avec l'utilisateur (vendeur)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculer le profit total de la vente
     */
    public function getTotalProfitAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return ($item->unit_price - $item->cost_price) * $item->quantity;
        });
    }

    /**
     * Calculer le pourcentage de marge
     */
    public function getTotalMarginPercentageAttribute(): float
    {
        $totalCost = $this->items->sum(function ($item) {
            return $item->cost_price * $item->quantity;
        });

        if ($totalCost == 0) {
            return 0;
        }

        return (($this->total_amount - $totalCost) / $totalCost) * 100;
    }

    /**
     * Générer automatiquement le numéro de vente
     */
    public static function generateSaleNumber(): string
    {
        $year = date('Y');
        $lastSale = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSale) {
            $lastNumber = intval(substr($lastSale->sale_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "VTE-{$year}-{$newNumber}";
    }

    /**
     * Scope pour les ventes d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    /**
     * Scope pour les ventes de cette semaine
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope pour les ventes de ce mois
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year);
    }

    /**
     * Scope pour les ventes complétées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
