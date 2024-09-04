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
            'name'=> 'Diabetes'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'name'=> 'Urinary'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'name'=> 'Digestive'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'name'=> 'Dermal'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'name'=> 'Respiratory'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'name'=> 'Vitamins'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'name'=> 'Alimentary'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'name'=> 'Antibiotics'
        ]);
        \App\Models\Category::create([
            'image' => '/images/placeholder.jpg',
            'name'=> 'Pressure'
        ]);
    }
}
