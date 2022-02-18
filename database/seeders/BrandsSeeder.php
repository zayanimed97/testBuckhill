<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $amaount = random_int(3, 10);
        for ($i=0; $i < $amaount; $i++) { 
            $title = $faker->name();
            $brand = new Brand();
                $brand->uuid = (string) Str::uuid();
                $brand->title = $title;
                $brand->slug = Str::slug($title);
            $brand->save();
        }
    }
}
