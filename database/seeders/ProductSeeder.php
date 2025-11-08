<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories and suppliers
        $smartphoneCategory = Category::where('name', 'Smartphones')->first();
        $tabletsCategory = Category::where('name', 'Tablettes')->first();
        $laptopCategory = Category::where('name', 'Ordinateurs portables')->first();
        $accessoriesCategory = Category::where('name', 'Accessoires PC')->first();
        $furnitureCategory = Category::where('name', 'Mobilier')->first();
        $officeSuppliesCategory = Category::where('name', 'Fournitures de bureau')->first();

        $techDistSupplier = Supplier::where('name', 'TechDist SA')->first();
        $expressoSupplier = Supplier::where('name', 'Expresso Distribution')->first();
        $sonatelSupplier = Supplier::where('name', 'Sonatel Business')->first();
        $auchanSupplier = Supplier::where('name', 'Auchan Sénégal')->first();

        $products = [
            // Smartphones
            [
                'name' => 'Samsung Galaxy S23',
                'description' => 'Smartphone haut de gamme avec écran AMOLED 6.1"',
                'sku' => 'SAM-S23-BLK',
                'barcode' => '8806094000000',
                'category_id' => $smartphoneCategory->id,
                'supplier_id' => $techDistSupplier->id,
                'unit_price' => 450000,
                'cost_price' => 380000,
                'quantity' => 25,
                'min_quantity' => 5,
                'max_quantity' => 50,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/SAM-S23-BLK/800/600',
            ],
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'iPhone Pro avec puce A17 Pro',
                'sku' => 'APL-IP15P-SIL',
                'barcode' => '0194253000000',
                'category_id' => $smartphoneCategory->id,
                'supplier_id' => $techDistSupplier->id,
                'unit_price' => 650000,
                'cost_price' => 550000,
                'quantity' => 15,
                'min_quantity' => 3,
                'max_quantity' => 30,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/APL-IP15P-SIL/800/600',
            ],
            [
                'name' => 'Xiaomi Redmi Note 13',
                'description' => 'Smartphone milieu de gamme avec batterie 5000mAh',
                'sku' => 'XIA-RN13-BLU',
                'barcode' => '6941812000000',
                'category_id' => $smartphoneCategory->id,
                'supplier_id' => $expressoSupplier->id,
                'unit_price' => 120000,
                'cost_price' => 95000,
                'quantity' => 8,
                'min_quantity' => 10,
                'max_quantity' => 40,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/XIA-RN13-BLU/800/600',
            ],

            // Tablettes
            [
                'name' => 'iPad Air M2',
                'description' => 'Tablette avec puce M2 et écran 11"',
                'sku' => 'APL-IPADAIR-M2',
                'barcode' => '0194253100000',
                'category_id' => $tabletsCategory->id,
                'supplier_id' => $techDistSupplier->id,
                'unit_price' => 380000,
                'cost_price' => 320000,
                'quantity' => 12,
                'min_quantity' => 5,
                'max_quantity' => 25,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/APL-IPADAIR-M2/800/600',
            ],
            [
                'name' => 'Samsung Galaxy Tab S9',
                'description' => 'Tablette Android avec S Pen inclus',
                'sku' => 'SAM-TABS9-GRY',
                'barcode' => '8806094100000',
                'category_id' => $tabletsCategory->id,
                'supplier_id' => $techDistSupplier->id,
                'unit_price' => 280000,
                'cost_price' => 235000,
                'quantity' => 18,
                'min_quantity' => 5,
                'max_quantity' => 30,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/SAM-TABS9-GRY/800/600',
            ],

            // Laptops
            [
                'name' => 'Dell XPS 15',
                'description' => 'Laptop professionnel 15.6" Intel i7, 16GB RAM',
                'sku' => 'DEL-XPS15-I7',
                'barcode' => '8845678000000',
                'category_id' => $laptopCategory->id,
                'supplier_id' => $sonatelSupplier->id,
                'unit_price' => 850000,
                'cost_price' => 720000,
                'quantity' => 7,
                'min_quantity' => 3,
                'max_quantity' => 15,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/DEL-XPS15-I7/800/600',
            ],
            [
                'name' => 'MacBook Pro 14"',
                'description' => 'MacBook Pro M3 Pro, 18GB RAM, 512GB SSD',
                'sku' => 'APL-MBP14-M3P',
                'barcode' => '0194253200000',
                'category_id' => $laptopCategory->id,
                'supplier_id' => $techDistSupplier->id,
                'unit_price' => 1200000,
                'cost_price' => 1050000,
                'quantity' => 5,
                'min_quantity' => 2,
                'max_quantity' => 10,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/APL-MBP14-M3P/800/600',
            ],
            [
                'name' => 'HP ProBook 450',
                'description' => 'Laptop business Intel i5, 8GB RAM',
                'sku' => 'HP-PB450-I5',
                'barcode' => '8856789000000',
                'category_id' => $laptopCategory->id,
                'supplier_id' => $sonatelSupplier->id,
                'unit_price' => 420000,
                'cost_price' => 350000,
                'quantity' => 3,
                'min_quantity' => 5,
                'max_quantity' => 20,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/HP-PB450-I5/800/600',
            ],

            // Accessoires PC
            [
                'name' => 'Logitech MX Master 3S',
                'description' => 'Souris sans fil ergonomique',
                'sku' => 'LOG-MXM3S-BLK',
                'barcode' => '0977234000000',
                'category_id' => $accessoriesCategory->id,
                'supplier_id' => $auchanSupplier->id,
                'unit_price' => 45000,
                'cost_price' => 35000,
                'quantity' => 30,
                'min_quantity' => 10,
                'max_quantity' => 50,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/LOG-MXM3S-BLK/800/600',
            ],
            [
                'name' => 'Clavier mécanique RGB',
                'description' => 'Clavier gaming mécanique avec rétroéclairage RGB',
                'sku' => 'GAM-KBRGB-BLK',
                'barcode' => '0977235000000',
                'category_id' => $accessoriesCategory->id,
                'supplier_id' => $auchanSupplier->id,
                'unit_price' => 35000,
                'cost_price' => 25000,
                'quantity' => 45,
                'min_quantity' => 15,
                'max_quantity' => 60,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/GAM-KBRGB-BLK/800/600',
            ],
            [
                'name' => 'Webcam HD 1080p',
                'description' => 'Caméra web Full HD avec micro intégré',
                'sku' => 'WEB-HD1080-BLK',
                'barcode' => '0977236000000',
                'category_id' => $accessoriesCategory->id,
                'supplier_id' => $auchanSupplier->id,
                'unit_price' => 28000,
                'cost_price' => 20000,
                'quantity' => 22,
                'min_quantity' => 8,
                'max_quantity' => 40,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/WEB-HD1080-BLK/800/600',
            ],

            // Mobilier
            [
                'name' => 'Chaise de bureau ergonomique',
                'description' => 'Chaise avec support lombaire réglable',
                'sku' => 'FUR-CHAIR-ERG',
                'barcode' => '1234567000000',
                'category_id' => $furnitureCategory->id,
                'supplier_id' => $auchanSupplier->id,
                'unit_price' => 85000,
                'cost_price' => 65000,
                'quantity' => 15,
                'min_quantity' => 5,
                'max_quantity' => 25,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/FUR-CHAIR-ERG/800/600',
            ],
            [
                'name' => 'Bureau ajustable',
                'description' => 'Bureau avec hauteur réglable électriquement',
                'sku' => 'FUR-DESK-ADJ',
                'barcode' => '1234568000000',
                'category_id' => $furnitureCategory->id,
                'supplier_id' => $auchanSupplier->id,
                'unit_price' => 250000,
                'cost_price' => 200000,
                'quantity' => 6,
                'min_quantity' => 3,
                'max_quantity' => 15,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/FUR-DESK-ADJ/800/600',
            ],

            // Fournitures de bureau
            [
                'name' => 'Ramette papier A4',
                'description' => 'Papier blanc 80g - 500 feuilles',
                'sku' => 'OFF-PAP-A4-500',
                'barcode' => '3456789000000',
                'category_id' => $officeSuppliesCategory->id,
                'supplier_id' => $auchanSupplier->id,
                'unit_price' => 3500,
                'cost_price' => 2500,
                'quantity' => 120,
                'min_quantity' => 30,
                'max_quantity' => 200,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/OFF-PAP-A4-500/800/600',
            ],
            [
                'name' => 'Stylos bille - Lot de 10',
                'description' => 'Stylos bille bleus',
                'sku' => 'OFF-PEN-BLU-10',
                'barcode' => '3456790000000',
                'category_id' => $officeSuppliesCategory->id,
                'supplier_id' => $auchanSupplier->id,
                'unit_price' => 2500,
                'cost_price' => 1500,
                'quantity' => 85,
                'min_quantity' => 25,
                'max_quantity' => 150,
                'status' => 'active',
                'image_url' => 'https://picsum.photos/seed/OFF-PEN-BLU-10/800/600',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
