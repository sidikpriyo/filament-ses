<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Vans', 'description' => 'Master Brand Vans'],
            ['name' => 'Converse', 'description' => 'Master Brand Converse'],
            ['name' => 'New Balance', 'description' => 'Master Brand New Balance'],
        ];

        foreach ($brands as $key => $brand) {
            Brand::create($brand);
        }
    }
}
