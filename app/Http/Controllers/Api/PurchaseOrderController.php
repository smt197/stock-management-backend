<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PurchaseOrder::with(['supplier', 'user', 'items.product']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by supplier
        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Search by reference
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('reference', 'like', "%{$search}%");
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        $query->orderBy('order_date', 'desc');

        // Pagination
        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $total = $query->count();
        $purchaseOrders = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'success' => true,
            'data' => $purchaseOrders,
            'total' => $total
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Generate unique reference
            $reference = $this->generateReference();

            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity_ordered'] * $item['unit_price'];
            }

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'reference' => $reference,
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(),
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            // Create purchase order items
            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity_ordered'] * $item['unit_price'],
                ]);
            }

            DB::commit();

            $purchaseOrder->load(['supplier', 'user', 'items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'data' => $purchaseOrder
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'user', 'items.product'])->find($id);

        if (!$purchaseOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::find($id);

        if (!$purchaseOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        // Can't update received or cancelled orders
        if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de modifier une commande reçue ou annulée'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'order_date' => 'sometimes|date',
            'expected_delivery_date' => 'nullable|date',
            'status' => 'sometimes|in:pending,confirmed,cancelled',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update purchase order
            $purchaseOrder->update($request->only([
                'supplier_id',
                'order_date',
                'expected_delivery_date',
                'status',
                'notes',
            ]));

            // Update items if provided
            if ($request->has('items')) {
                // Delete old items
                $purchaseOrder->items()->delete();

                // Calculate total amount
                $totalAmount = 0;
                foreach ($request->items as $item) {
                    $totalAmount += $item['quantity_ordered'] * $item['unit_price'];

                    // Create new items
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_id' => $item['product_id'],
                        'quantity_ordered' => $item['quantity_ordered'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['quantity_ordered'] * $item['unit_price'],
                    ]);
                }

                $purchaseOrder->update(['total_amount' => $totalAmount]);
            }

            DB::commit();

            $purchaseOrder->load(['supplier', 'user', 'items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Commande mise à jour avec succès',
                'data' => $purchaseOrder
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::find($id);

        if (!$purchaseOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        // Can't delete received orders
        if ($purchaseOrder->status === 'received') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer une commande reçue'
            ], 400);
        }

        $purchaseOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commande supprimée avec succès'
        ]);
    }

    /**
     * Receive items from a purchase order.
     */
    public function receive(Request $request, string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::with('items.product')->find($id);

        if (!$purchaseOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        if ($purchaseOrder->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de recevoir une commande annulée'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:1',
            'actual_delivery_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Receive items and update stock
            foreach ($request->items as $itemData) {
                $item = PurchaseOrderItem::find($itemData['item_id']);

                if ($item->purchase_order_id != $purchaseOrder->id) {
                    throw new \Exception("L'item ne fait pas partie de cette commande");
                }

                $quantityToReceive = $itemData['quantity_received'];
                $remainingQuantity = $item->quantity_ordered - $item->quantity_received;

                if ($quantityToReceive > $remainingQuantity) {
                    throw new \Exception("Quantité reçue supérieure à la quantité restante pour {$item->product->name}");
                }

                // Update item quantity received
                $item->increment('quantity_received', $quantityToReceive);

                // Update product stock
                $product = Product::find($item->product_id);
                $product->increment('quantity', $quantityToReceive);

                // Create stock movement
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $quantityToReceive,
                    'reference' => $purchaseOrder->reference,
                    'notes' => "Réception commande fournisseur {$purchaseOrder->reference}",
                ]);
            }

            // Update purchase order status
            if ($purchaseOrder->isFullyReceived()) {
                $purchaseOrder->update([
                    'status' => 'received',
                    'actual_delivery_date' => $request->actual_delivery_date ?? now(),
                ]);
            } elseif ($purchaseOrder->isPartiallyReceived()) {
                $purchaseOrder->update([
                    'status' => 'partially_received',
                ]);
            }

            DB::commit();

            $purchaseOrder->load(['supplier', 'user', 'items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Réception enregistrée avec succès',
                'data' => $purchaseOrder
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique reference for purchase order.
     */
    private function generateReference(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastOrder = PurchaseOrder::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastOrder ? intval(substr($lastOrder->reference, -4)) + 1 : 1;

        return sprintf('CMD-%s%s-%04d', $year, $month, $number);
    }
}
