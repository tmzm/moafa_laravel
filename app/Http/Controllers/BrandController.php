<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        self::ok(Brand::withCount('products')->latest()->get());
    }

    public function show($brand_id)
    {
        $brand = Brand::find($brand_id);

        if($brand) {
            self::ok($brand);
        }

        self::notFound();
    }

    public function create(Request $request)
    {
        $image = self::save_image_to_public_directory($request);
        $brand = Brand::create([
            'name' => $request->name,
            'meta_title' => $request->meta_title,
            'meta_subtitle' => $request->meta_subtitle,
            'meta_description' => $request->meta_description,
            'image' => $image,
            ]);

        self::ok($brand);
    }

    public function edit(Request $request, $brand_id)
    {
        $brand = Brand::find($brand_id);
        
        $image = null;

        if ($request->hasfile('image')) {
            $image = self::save_image_to_public_directory($request);
        }

        if($brand) {
            if($request->name)
                $brand->name = $request->name;
            
            if($image)
                $brand->image = $image;

            if($request->meta_title)
                $brand->meta_title = $request->meta_title;
            
            if($request->meta_subtitle)
                $brand->meta_subtitle = $request->meta_subtitle;
            
            if($request->meta_description)
                $brand->meta_description = $request->meta_description;

            $brand->save();
        }

        self::ok($brand);
    }

    public function destroy($brand_id)
    {
        $brand = Brand::find($brand_id);

        if($brand){
            $brand->delete();

            self::ok();
        }

        self::notFound();
    }
}
