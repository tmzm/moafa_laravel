<?php

namespace App\Http\Helpers;

use Illuminate\Support\Str;
use App\Enums\ReturnMessages;
use App\Http\Controllers\NotificationController;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Coupon;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Mockery\Undefined;

trait CreateUpdateHelper
{
    public function update_order_status($order,$request) : void
    {
        $order->update([
            'status' => $request['status'],
            'payment_status' => $request['payment_status']
        ]);

        $user = User::find($order->user_id);

        if($user->device_key !== null)
            self::send_order_notification_to_user($request,$user);
    }

    public function increase_every_product_by_quantity($order): void
    {
        $orderItems = $order->order_items;
        foreach ($orderItems as $item){
            $p = Product::find($item->product->id);
            if($p->is_quantity){
                $p->quantity += $item->quantity;
                $p->save();
            }
        }
    }

    public function create_order_item_and_reduce_every_product_by_order_quantity($products,$order)
    {
        foreach ($products as $p) {
            OrderItem::create([
                'product_id' => $p['id'],
                'order_id' => $order->id,
                'quantity' => $p['quantity']
            ]);
            $pr = Product::find($p['id']);
            if($pr->is_quantity){
                $pr->quantity -= $p['quantity'];
                $pr->save();
            }
        }
    }

    public function decresue_total_price_before_delete_order_item($orderItem): void
    {
        $product = $orderItem->product;
        $product->quantity += $orderItem->quantity;
        $product->save();
    }

    public function update_every_order_item_quantity($request,$order): void
    {
        // $temp = $request;
        // check new order quantity if not biggest than product quantity
        foreach ($request as $p){
            $product = Product::find($p['id']);
            $orderItem = OrderItem::firstWhere('product_id',$p['id']);
            if($product->is_quantity){
                $product->quantity += $orderItem->quantity;
                if($product->quantity < $p['quantity'])
                    self::unHandledError('some of the products could not be updated');
            }
        }
        foreach ($request as $p) {
            $orderItem = OrderItem::firstWhere('product_id',$p['id']);
            $product = Product::find($p['id']);
            
            if($product->is_quantity)
               $product->quantity += $orderItem->quantity;

            $orderItem->update([
                'quantity' => $p['quantity']
            ]);
            $order->save();

            if($product->is_quantity){
                $product->quantity -= $p['quantity'];
                $product->save();
            }
        }
    }

    public function create_user($data)
    {
        return User::create([
            'name' => $data['name'],
            'phone_number' => $data['phone_number'],
            'password' => bcrypt($data['password']),
            'role' => $data['role'],
            'device_key' => $data['device_key'] ?? null,
        ]);
    }

    public function create_order_by_request($request): void
    {

        isset($request['products']) ? $products = $request['products'] : self::unHandledError('No Products found');

        self::check_products_quantity($products);

        $coupon = null;

        if(isset($request['coupon_id'])){
            $coupon = Coupon::find($request['coupon_id']);
        }else if(isset($request['coupon_code'])){
            $coupon = Coupon::firstWhere('code',$request['coupon_code']);
        }

        $order = Order::create([
            'coupon_id' => $coupon?->id ?? false,
            'is_prescription' => $request['is_prescription'] ?? false,
            'accepted_by_user' => $request['accepted_by_user'] ?? true,
            'time' => $request['time'] ?? null,
            'is_time' => $request['is_time'] ?? false,
            'user_id' => $request['user_id'] ?? $request->user()->id,
            'location_id' => $request['location_id'],
        ]);

        self::create_order_item_and_reduce_every_product_by_order_quantity($products,$order);

        $order = Order::find($order->id);

        self::ok($order);
    }

    public function update_order_by_request_and_order($request,$order_id): void
    {
        $order = Order::firstWhere('id',$order_id);

        if(!$order)
            self::notFound();

        if($request['status'] ?? $request['payment_status'] ?? false)
            self::update_order_status($order,$request);

        if($request['products'] ?? false)
            self::update_every_order_item_quantity($request['products'],$order);

        self::ok($order);
    }

    public function delete_order($request,$order_id): void
    {
        $order = Order::find($order_id);

        if($order) {
            self::increase_every_product_by_quantity($order);
            $order->delete();
            self::ok();
        }
        self::notFound();
    }

    public function delete_order_item($order_item_id)
    {
        $orderItem = OrderItem::find($order_item_id);

        if($orderItem){
            self::decresue_total_price_before_delete_order_item($orderItem);
            $orderItem->delete();
            self::ok();
        }
        self::notFound();
    }

    public function create_product($request): void
    {
        $data = $request->validated();

        $image = self::save_image_to_public_directory($request);

        $data['image'] = $image;

        // Generate a unique id
        $uniqueId = Str::uuid()->toString();
        // Clean the name and create slug
        $slug = Str::slug($data['name']);
        // Check if slug already exists
        $count = Product::where('slug', $slug)->count();
        // If slug already exists, append a number to make it unique
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        // Append the unique id to the slug
        $slug .= '-' . $uniqueId;

        $product = Product::create([
            'name' => $data['name'],
            'brand_id' => $data['brand_id'],
            'slug' => $slug,
            'quantity' => $data['quantity'],
            'is_quantity' => $data['is_quantity'],
            'offer' => $data['offer'],
            'is_offer' => $data['is_offer'],
            'description' => $data['description'],
            'meta_description' => $data['meta_description'] ?? null,
            'meta_subtitle' => $data['meta_subtitle'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'price' => $data['price'],
            'expiration' => $data['expiration'],
            'image' => $data['image'] ?? null,
        ]);

        foreach($data['categories'] as $category_id){
            CategoryProduct::create([
                'category_id' => $category_id,
                'product_id' => $product->id
            ]);
        }

        self::ok($product);
    }

    public function update_product($request,$product_id): void
    {
        $product = Product::find($product_id);

        if(!$product)
            self::notFound();

        $data = $request->validated();

        $image = self::save_image_to_public_directory($request);

        if($image !== '/images/noImage.jpg')
            $data['image'] = $image;

        foreach($product->category_products as $category){
            $isExists = 0;

            foreach($data['categories'] as $c){

                if($c == $category->id){
                    $isExists++;
                }

                
            }

            if($isExists == 0){
                $category->delete();
            }
        }

        foreach($data['categories'] as $category){
            $isExists = 0;

            foreach($product->category_products as $c){
                if($c->id == $category){
                    $isExists++;
                }
            }

            if($isExists == 0){
                CategoryProduct::create([
                    'product_id'=>$product->id,
                    'category_id'=>$category
                ]);
            }
        }

        $data = Arr::except($data, ['categories']);

        $product->update($data);

        self::ok($product);
    }

    /**
     * @throws GuzzleException
     */
    public function delete_product($request, $product_id): void
    {
        $product = Product::find($product_id);

        if($product) {
            $users = User::byProductOrders($product)->get();

            foreach ($users as $user){
                if($user->device_key !== null){
                    (new NotificationController)->notify(
                        'series order changes',
                        'an order product: '.$product->commercial_name .' no longer available',
                        $user
                    );
                }
            }

            self::delete_image(public_path($product->image));

            $product->delete();

            self::ok();
        }

        self::notFound();
    }

    public function create_favorite($user_id,$product_id): void
    {
        if(Favorite::where('user_id',$user_id)?->firstWhere('product_id',$product_id))
            self::unHandledError('favorite already exists');

        $favorite = Favorite::create([
            'product_id' => $product_id,
            'user_id' => $user_id
        ]);

        $favorite ? self::ok($favorite) : self::unHandledError("Couldn't create this favorite");
    }

    public function delete_user_favorite($favorite_id,$user_id): void
    {
        $favorite = Favorite::where('id',$favorite_id)?->firstWhere('user_id',$user_id);

        if($favorite) {
            $favorite->delete();
            self::ok();
        }

        self::notFound();
    }

    public function create_category($request)
    {
        $validator = validator($request->all(),[
            'name' => 'required|unique:categories,name',
            'image' => 'image|mimes:jpg,jpeg,png'
        ]);

        $data = $validator->validated();

        $data['image'] = self::save_image_to_public_directory($request);

        Category::create($data);

        self::ok();
    }

    public function edit_category($request,$category_id)
    {
        $category = Category::find($category_id);

        $validator = validator($request->all(),[
            'name' => 'unique:categories,name',
            'image' => 'image|mimes:jpg,jpeg,png'
        ]);

        $data = $validator->validated();

        $data['image'] = null;

        if($request->hasfile('image'))
            $data['image'] = self::save_image_to_public_directory($request);

        if($category){
            $category->update($data);
    
            self::ok();
        }

        self::notFound();
    }

    public function destroy_category($category_id)
    {
        $category = Category::find($category_id);

        if($category){
            $category->delete();
    
            self::ok();
        }

        self::notFound();
    }

}
