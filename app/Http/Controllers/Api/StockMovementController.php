<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockMovement::with(['product', 'user']);

        // Filter by product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $movements
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);

            // Calculate new quantity based on movement type
            $oldQuantity = $product->quantity;
            $newQuantity = $oldQuantity;

            switch ($request->type) {
                case 'in':
                    $newQuantity += $request->quantity;
                    break;
                case 'out':
                    if ($oldQuantity < $request->quantity) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Quantité insuffisante en stock'
                        ], 422);
                    }
                    $newQuantity -= $request->quantity;
                    break;
                case 'adjustment':
                    $newQuantity = $request->quantity;
                    break;
            }

            // Update product quantity
            $product->quantity = $newQuantity;
            $product->save();

            // Create stock movement
            $movement = StockMovement::create($validator->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mouvement de stock créé avec succès',
                'data' => $movement->load(['product', 'user'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du mouvement de stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $movement = StockMovement::with(['product', 'user'])->find($id);

        if (!$movement) {
            return response()->json([
                'success' => false,
                'message' => 'Mouvement de stock non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $movement
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $movement = StockMovement::find($id);

        if (!$movement) {
            return response()->json([
                'success' => false,
                'message' => 'Mouvement de stock non trouvé'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $movement->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Mouvement de stock mis à jour avec succès',
            'data' => $movement->load(['product', 'user'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $movement = StockMovement::find($id);

        if (!$movement) {
            return response()->json([
                'success' => false,
                'message' => 'Mouvement de stock non trouvé'
            ], 404);
        }

        // Note: Deleting a stock movement doesn't reverse the product quantity
        // You may want to implement a reversal mechanism instead
        $movement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mouvement de stock supprimé avec succès'
        ]);
    }
}
