<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'TechDist SA',
                'email' => 'contact@techdist.sn',
                'phone' => '+221 33 823 45 67',
                'address' => '123 Avenue Hassan II',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'website' => 'https://www.techdist.sn',
                'contact_person' => 'Moussa Diop',
                'status' => 'active',
            ],
            [
                'name' => 'Expresso Distribution',
                'email' => 'info@expresso-dist.com',
                'phone' => '+221 33 845 12 34',
                'address' => '45 Rue de la République',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'website' => 'https://www.expresso-dist.com',
                'contact_person' => 'Fatou Sall',
                'status' => 'active',
            ],
            [
                'name' => 'Sonatel Business',
                'email' => 'business@sonatel.sn',
                'phone' => '+221 33 839 90 00',
                'address' => '46 Boulevard de la République',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'website' => 'https://www.sonatel.sn',
                'contact_person' => 'Amadou Ba',
                'status' => 'active',
            ],
            [
                'name' => 'Auchan Sénégal',
                'email' => 'fournitures@auchan.sn',
                'phone' => '+221 33 827 50 00',
                'address' => 'Sea Plaza, Route de Ngor',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'website' => 'https://www.auchan.sn',
                'contact_person' => 'Khadija Ndiaye',
                'status' => 'active',
            ],
            [
                'name' => 'Casino Distribution',
                'email' => 'contact@casino.sn',
                'phone' => '+221 33 869 12 00',
                'address' => 'Centre Commercial Touba Sandaga',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'website' => null,
                'contact_person' => 'Ibrahima Fall',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
