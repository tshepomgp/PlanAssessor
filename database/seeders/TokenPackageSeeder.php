<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TokenPackage;

class TokenPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter Pack',
                'description' => 'Perfect for small architectural firms',
                'tokens' => 100,
                'price' => 150.00,
                'bonus_percentage' => 0,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional Pack',
                'description' => 'Most popular choice for active architects',
                'tokens' => 500,
                'price' => 600.00,
                'bonus_percentage' => 20,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise Pack',
                'description' => 'For large firms with high volume assessments',
                'tokens' => 1000,
                'price' => 1000.00,
                'bonus_percentage' => 30,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Premium Pack',
                'description' => 'Maximum value for high-volume users',
                'tokens' => 2500,
                'price' => 2200.00,
                'bonus_percentage' => 40,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($packages as $packageData) {
            TokenPackage::updateOrCreate(
                ['name' => $packageData['name']],
                $packageData
            );
        }

        $this->command->info('Token packages created successfully!');
    }
}