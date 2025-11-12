<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🛒 Création de ventes de test...\n";

        // Récupérer un utilisateur et des produits
        $user = User::first();
        if (!$user) {
            echo "❌ Aucun utilisateur trouvé. Exécutez d'abord DatabaseSeeder.\n";
            return;
        }

        $products = Product::where('quantity', '>', 5)->take(5)->get();
        if ($products->isEmpty()) {
            echo "❌ Aucun produit avec stock suffisant trouvé.\n";
            return;
        }

        // Créer 3 ventes
        for ($i = 1; $i <= 3; $i++) {
            DB::beginTransaction();

            try {
                // Sélectionner 2-3 produits aléatoires
                $numItems = rand(2, 3);
                $selectedProducts = $products->random($numItems);

                $totalAmount = 0;
                $saleItemsData = [];

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);
                    $subtotal = $product->unit_price * $quantity;
                    $totalAmount += $subtotal;

                    $saleItemsData[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ];
                }

                // Créer la vente
                $sale = Sale::create([
                    'sale_number' => Sale::generateSaleNumber(),
                    'customer_name' => ['Jean Dupont', 'Marie Martin', 'Paul Ngono', null][$i % 4],
                    'customer_phone' => ['690123456', '677654321', null][$i % 3],
                    'total_amount' => $totalAmount,
                    'payment_method' => ['cash', 'mobile_money', 'card'][rand(0, 2)],
                    'payment_status' => 'paid',
                    'amount_paid' => $totalAmount,
                    'amount_due' => 0,
                    'notes' => $i === 1 ? 'Vente de test - Client régulier' : null,
                    'user_id' => $user->id,
                    'sale_date' => now()->subDays(rand(0, 7)),
                    'status' => 'completed',
                ]);

                // Créer les items et décrémenter le stock
                foreach ($saleItemsData as $itemData) {
                    $product = $itemData['product'];

                    $sale->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $product->unit_price,
                        'cost_price' => $product->cost_price,
                        'subtotal' => $itemData['subtotal'],
                    ]);

                    // Décrémenter le stock
                    $product->decrement('quantity', $itemData['quantity']);

                    // Créer un mouvement de stock
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => -$itemData['quantity'],
                        'reference' => "Sale-{$sale->id}",
                        'notes' => "Vente {$sale->sale_number}",
                        'user_id' => $user->id,
                    ]);
                }

                DB::commit();

                echo "✅ Vente #{$sale->sale_number} créée: " . number_format($totalAmount, 0) . " FCFA\n";
                echo "   - Items: " . $sale->items->count() . "\n";
                echo "   - Profit: " . number_format($sale->total_profit, 0) . " FCFA\n";
                echo "   - Marge: " . number_format($sale->total_margin_percentage, 2) . "%\n\n";

            } catch (\Exception $e) {
                DB::rollBack();
                echo "❌ Erreur lors de la création de la vente: " . $e->getMessage() . "\n";
            }
        }

        echo "\n📊 Résumé des ventes:\n";
        echo "Total ventes: " . Sale::count() . "\n";
        echo "CA total: " . number_format(Sale::sum('total_amount'), 0) . " FCFA\n";
        echo "Profit total: " . number_format(Sale::with('items')->get()->sum(function ($sale) {
            return $sale->total_profit;
        }), 0) . " FCFA\n";
    }
}
