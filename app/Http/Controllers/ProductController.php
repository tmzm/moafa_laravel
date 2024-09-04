<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\ProductsImport;
use App\Models\Product;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     *
     */
    public function index()
    {
        self::filter_products(request(['search', 'categories','sort', 'take', 'skip','price']));
    }

    public function index_top_sellers()
    {
        self::ok(Product::withCount('order_items')->orderByDesc('order_items_count')->take(10)->get());
    }

    public function get_price($product_id)
    {
        $product = Product::where('id',$product_id)->select('price','is_offer','offer')->first();
        
        if($product->is_offer){
            $price = $product->price - $product->price * ($product->offer / 100);
        }else{
            $price = $product->price;
        }

        self::ok($price);
    }

    public function get_image($product_id)
    {
        self::ok(Product::where('id',$product_id)->select('image')->first()->image);
    }

    public function get_total_count()
    {
        self::ok(self::total_count(request(['search', 'categories','price']))->count());
    }


    /**
     * @param CreateProductRequest $request
     */
    public function create(CreateProductRequest $request)
    {
        self::create_product($request);
    }


    /**
     * @param $slug
     */
    public function show($slug)
    {
        self::ok(Product::where('slug',$slug)->first());
    }

    public function show_by_id($product_id)
    {
        self::ok(Product::find($product_id));
    }

    /**
     * @param UpdateProductRequest $request
     * @param $product_id
     */
    public function update(UpdateProductRequest $request, $product_id)
    {
        self::update_product($request, $product_id);
    }


    /**
     * @param Request $request
     * @param $product_id
     * @throws GuzzleException
     */
    public function destroy(Request $request, $product_id)
    {
        self::delete_product($request,$product_id);
    }

    public function import(Request $request) 
    {
        Excel::import(new ProductsImport, $request->file('excel'));
        
        return self::ok();
    }
}
