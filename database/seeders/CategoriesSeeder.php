<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $amaount = random_int(1, 10);
        for ($i=0; $i < $amaount; $i++) { 
            $title = $faker->text(50);
            $category = new Category();
                $category->uuid = (string) Str::uuid();
                $category->title = $title;
                $category->slug = Str::slug($title);
            $category->save();
        }
    }
}
