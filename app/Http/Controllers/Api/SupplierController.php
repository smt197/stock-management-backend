<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::with('products')->orderBy('name');

        // Pagination
        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $total = $query->count();
        $suppliers = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'success' => true,
            'data' => $suppliers,
            'total' => $total
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $supplier = Supplier::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Fournisseur créé avec succès',
            'data' => $supplier
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $supplier = Supplier::with('products')->find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Fournisseur non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $supplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Fournisseur non trouvé'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:255',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $supplier->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Fournisseur mis à jour avec succès',
            'data' => $supplier
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Fournisseur non trouvé'
            ], 404);
        }

        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fournisseur supprimé avec succès'
        ]);
    }
}
