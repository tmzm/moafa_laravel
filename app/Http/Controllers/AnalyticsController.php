<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        self::ok([
            'categories_count' => Category::count(),
            'products_count' => Product::count(),
            'brands_count' => Brand::count(),
            'orders_count' => Order::count(),
            'coupons_count' => Coupon::count(),
            'prescriptions_count' => Prescription::count(),
            'rates_count' => Rate::count(),
        ]);
    }

    public function sales_categories()
    {
        $categorySales = OrderItem::select('categories.name', DB::raw('SUM(order_items.quantity * (CASE WHEN products.is_offer = true THEN products.price * (1 - (products.offer / 100)) ELSE products.price END)) as total_sales'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('category_products', 'products.id', '=', 'category_products.product_id')
            ->join('categories', 'category_products.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->orderBy('total_sales', 'desc')
            ->get();

        $topCategories = $categorySales->take(2);

        $otherCategoriesTotal = $categorySales->skip(2)->sum('total_sales');

        $result = $topCategories->toArray();
        $result[] = [
            'name' => 'Other Categories',
            'total_sales' => $otherCategoriesTotal,
        ];

        self::ok($result);
    }
}
