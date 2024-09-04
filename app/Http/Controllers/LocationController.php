<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLocationRequest;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function create(CreateLocationRequest $request)
    {
        $data = $request->validated();

        $location = Location::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'type' => $data['type'],
            'user_id' => $request->user()->id
        ]);

        self::ok($location);
    }

    public function index(Request $request)
    {
        self::ok(Location::where('user_id', $request->user()->id)->get());
    }
}
