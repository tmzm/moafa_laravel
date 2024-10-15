<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'status' => 1,
            'name'=> 'Diabetes'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'status' => 1,
            'name'=> 'Urinary'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'status' => 1,
            'name'=> 'Digestive'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'status' => 1,
            'name'=> 'Dermal'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'status' => 1,
            'name'=> 'Respiratory'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'status' => 1,
            'name'=> 'Vitamins'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'status' => 1,
            'name'=> 'Alimentary'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'status' => 1,
            'name'=> 'Antibiotics'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'status' => 1,
            'name'=> 'Pressure'
        ]);
    }
}
