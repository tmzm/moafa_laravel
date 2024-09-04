<?php

namespace Database\Seeders;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createNewRecord('Vasolol',5);
        $this->createNewRecord('Vento-Aid',9);
        $this->createNewRecord('Uromax',2);
        $this->createNewRecord('Smectal',3);
        $this->createNewRecord('Otocalm',5);
        $this->createNewRecord('Nailfen',4);
        $this->createNewRecord('Artral Mineral',7);
        $this->createNewRecord('Mentogel',8);
        $this->createNewRecord('Diadap',1);
        $this->createNewRecord('Colo Clean',3);
        $this->createNewRecord('Cralyl',8);
        $this->createNewRecord('Calcicomb',7);
        $this->createNewRecord('New Aid',8);
        $this->createNewRecord('Lukast',5);
        $this->createNewRecord('Coteptal',9);
        $this->createNewRecord('Vitamen-C Alfares',6);
        $this->createNewRecord('Artral',7);
        $this->createNewRecord('Azitrolyd',8);
    }
    public function createNewRecord($name,$cid){
        $maximumDate = Carbon::create(2025, 12, 31);
        $randomDate = Carbon::createFromTimestamp(mt_rand(time(), $maximumDate->timestamp));

        $imagePath = 'D:/testimages/'.$name.'.png';
        $imagName = time() . '_' . $name . '.png';
        copy($imagePath, public_path('images/' . $imagName));
        $image = '/images/' .  $imagName;

        \App\Models\Product::create([
            'scientific_name' => $name,
            'commercial_name' => $name,
            'company_name' => fake()->company(),
            'category_id' => $cid,
            'image' => $image,
            'quantity'=>fake()->numberBetween(10,100),
            'price'=>fake()->numberBetween(1,7) * 5000,
            'expiration'=>$randomDate->format('Y-m-d')
        ]);
    }
}
