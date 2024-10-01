<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Coupon;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PointsTransfer;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new CategorySeeder)->run();

        User::factory(30)->create();

        Coupon::factory(15)->create();

        Brand::factory(15)->create();

        Order::factory(30)->create();

        Prescription::factory(60)->create();

        Product::factory(60)->create();

        OrderItem::factory(120)->create();

        Location::factory(20)->create();

        CategoryProduct::factory(90)->create();

        PointsTransfer::factory(60)->create();
    }
}
