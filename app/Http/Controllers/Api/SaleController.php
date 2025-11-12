<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Liste des ventes avec filtres
     */
    public function index(Request $request): JsonResponse
    {
        $query = Sale::with(['items.product', 'user']);

        // Filtre par statut
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par statut de paiement
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filtre par méthode de paiement
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filtre par période
        if ($request->has('period')) {
            switch ($request->period) {
                case 'today':
                    $query->today();
                    break;
                case 'week':
                    $query->thisWeek();
                    break;
                case 'month':
                    $query->thisMonth();
                    break;
            }
        }

        // Filtre par date personnalisée
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('sale_date', [$request->date_from, $request->date_to]);
        }

        // Recherche par numéro de vente ou nom client
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $query->orderBy('sale_date', 'desc')->orderBy('id', 'desc');

        // Pagination
        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $total = $query->count();
        $sales = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Ajouter les calculs de profit
        $sales->each(function ($sale) {
            $sale->total_profit = $sale->total_profit;
            $sale->total_margin_percentage = $sale->total_margin_percentage;
        });

        return response()->json([
            'success' => true,
            'data' => $sales,
            'total' => $total
        ]);
    }

    /**
     * Créer une nouvelle vente
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,mobile_money,card,credit',
            'payment_status' => 'required|in:paid,pending,partial',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Vérifier la disponibilité du stock pour tous les produits
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Produit non trouvé: {$item['product_id']}");
                }

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour {$product->name}. Disponible: {$product->quantity}, Demandé: {$item['quantity']}");
                }
            }

            // Calculer le total
            $totalAmount = 0;
            $saleItemsData = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $product->unit_price * $item['quantity'];
                $totalAmount += $subtotal;

                $saleItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->unit_price,
                    'cost_price' => $product->cost_price,
                    'subtotal' => $subtotal,
                ];
            }

            // Calculer amount_due
            $amountDue = max(0, $totalAmount - $request->amount_paid);

            // Créer la vente
            $sale = Sale::create([
                'sale_number' => Sale::generateSaleNumber(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'amount_paid' => $request->amount_paid,
                'amount_due' => $amountDue,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
                'sale_date' => now(),
                'status' => 'completed',
            ]);

            // Créer les items et décrémenter le stock
            foreach ($saleItemsData as $itemData) {
                $sale->items()->create($itemData);

                // Décrémenter le stock
                $product = Product::find($itemData['product_id']);
                $product->decrement('quantity', $itemData['quantity']);

                // Créer un mouvement de stock
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => -$itemData['quantity'],
                    'reference_type' => 'App\Models\Sale',
                    'reference_id' => $sale->id,
                    'notes' => "Vente {$sale->sale_number}",
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();

            // Recharger la vente avec les relations
            $sale->load(['items.product', 'user']);
            $sale->total_profit = $sale->total_profit;
            $sale->total_margin_percentage = $sale->total_margin_percentage;

            return response()->json([
                'success' => true,
                'message' => 'Vente créée avec succès',
                'data' => $sale
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Afficher les détails d'une vente
     */
    public function show($id): JsonResponse
    {
        $sale = Sale::with(['items.product', 'user'])->find($id);

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Vente non trouvée'
            ], 404);
        }

        $sale->total_profit = $sale->total_profit;
        $sale->total_margin_percentage = $sale->total_margin_percentage;

        return response()->json([
            'success' => true,
            'data' => $sale
        ]);
    }

    /**
     * Annuler une vente (remettre le stock)
     */
    public function cancel($id): JsonResponse
    {
        $sale = Sale::with('items')->find($id);

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Vente non trouvée'
            ], 404);
        }

        if ($sale->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cette vente est déjà annulée'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Remettre le stock pour chaque item
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('quantity', $item->quantity);

                    // Créer un mouvement de stock inverse
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'adjustment',
                        'quantity' => $item->quantity,
                        'reference_type' => 'App\Models\Sale',
                        'reference_id' => $sale->id,
                        'notes' => "Annulation vente {$sale->sale_number}",
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            // Marquer la vente comme annulée
            $sale->update(['status' => 'cancelled']);

            DB::commit();

            $sale->load(['items.product', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Vente annulée avec succès. Le stock a été remis à jour.',
                'data' => $sale
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Statistiques des ventes
     */
    public function statistics(Request $request): JsonResponse
    {
        $period = $request->input('period', 'today');

        $query = Sale::completed();

        switch ($period) {
            case 'today':
                $query->today();
                break;
            case 'week':
                $query->thisWeek();
                break;
            case 'month':
                $query->thisMonth();
                break;
        }

        $sales = $query->with('items')->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        $totalProfit = $sales->sum(function ($sale) {
            return $sale->total_profit;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total_sales' => $totalSales,
                'total_revenue' => $totalRevenue,
                'total_profit' => $totalProfit,
                'average_sale' => $totalSales > 0 ? $totalRevenue / $totalSales : 0,
            ]
        ]);
    }
}
