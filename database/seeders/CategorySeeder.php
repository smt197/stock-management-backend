<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Électronique',
                'description' => 'Produits électroniques et accessoires',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Informatique',
                'description' => 'Matériel et accessoires informatiques',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Mobilier',
                'description' => 'Meubles de bureau et maison',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Fournitures de bureau',
                'description' => 'Fournitures et accessoires de bureau',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Alimentation',
                'description' => 'Produits alimentaires et boissons',
                'parent_id' => null,
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Add some subcategories
        $electronicsCategory = Category::where('name', 'Électronique')->first();
        $informaticsCategory = Category::where('name', 'Informatique')->first();

        $subcategories = [
            [
                'name' => 'Smartphones',
                'description' => 'Téléphones mobiles',
                'parent_id' => $electronicsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Tablettes',
                'description' => 'Tablettes électroniques',
                'parent_id' => $electronicsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Ordinateurs portables',
                'description' => 'Laptops et notebooks',
                'parent_id' => $informaticsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Accessoires PC',
                'description' => 'Claviers, souris, etc.',
                'parent_id' => $informaticsCategory->id,
                'status' => 'active',
            ],
        ];

        foreach ($subcategories as $subcategory) {
            Category::create($subcategory);
        }
    }
}
