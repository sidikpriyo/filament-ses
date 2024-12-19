<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $series = array(
            array(
                "id" => 1,
                "brand_id" => 1,
                "name" => "Old Skool Classic",
                "price" => 350000.00,
                "created_at" => "2024-12-17 03:32:49",
                "updated_at" => "2024-12-17 03:32:49",
            ),
            array(
                "id" => 2,
                "brand_id" => 1,
                "name" => " Sk8 High Classic",
                "price" => 450000.00,
                "created_at" => "2024-12-17 03:32:59",
                "updated_at" => "2024-12-17 03:32:59",
            ),
            array(
                "id" => 3,
                "brand_id" => 1,
                "name" => "Authentic Classic",
                "price" => 350000.00,
                "created_at" => "2024-12-17 03:33:11",
                "updated_at" => "2024-12-17 03:33:11",
            ),
            array(
                "id" => 4,
                "brand_id" => 3,
                "name" => "New Balance 327",
                "price" => 750000.00,
                "created_at" => "2024-12-17 03:33:20",
                "updated_at" => "2024-12-17 03:33:20",
            ),
            array(
                "id" => 5,
                "brand_id" => 3,
                "name" => "New Balance 530",
                "price" => 1500000.00,
                "created_at" => "2024-12-17 03:33:35",
                "updated_at" => "2024-12-17 03:33:35",
            ),
            array(
                "id" => 6,
                "brand_id" => 3,
                "name" => "New Balance 550",
                "price" => 999000.00,
                "created_at" => "2024-12-17 03:33:45",
                "updated_at" => "2024-12-17 03:33:45",
            ),
            array(
                "id" => 7,
                "brand_id" => 2,
                "name" => "Taylor 70s High",
                "price" => 949000.00,
                "created_at" => "2024-12-17 03:33:56",
                "updated_at" => "2024-12-17 03:33:56",
            ),
            array(
                "id" => 8,
                "brand_id" => 2,
                "name" => "Taylor All Star High",
                "price" => 450000.00,
                "created_at" => "2024-12-17 03:34:04",
                "updated_at" => "2024-12-17 03:34:04",
            ),
            array(
                "id" => 9,
                "brand_id" => 2,
                "name" => "Taylor 70s Low",
                "price" => 849000.00,
                "created_at" => "2024-12-17 03:34:15",
                "updated_at" => "2024-12-17 03:34:15",
            ),
        );

        foreach ($series as $item) {
            \App\Models\Series::create($item);
        }
    }
}
