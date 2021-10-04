<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Category::create(
            [
                'name' => 'Toys',
                'store_id' => 1,

            ],
        );

        \App\Models\Category::create(
            [
                'name' => 'Detergent',
                'store_id' => 1,

            ],
        );
        \App\Models\Category::create(
            [
                'name' => 'Clothes',
                'store_id' => 1,

            ],
        );
        \App\Models\Category::create(
            [
                'name' => 'Toys',
                'store_id' => 2,

            ],
        );
        \App\Models\Category::create(

            [
                'name' => 'Detergent',
                'store_id' => 2,

            ],
        );
        \App\Models\Category::create(
            [
                'name' => 'Clothes',
                'store_id' => 2,

            ],
        );
        \App\Models\Category::create(
            [
                'name' => 'Home accessories',
                'store_id' => 3,

            ],
        );
        \App\Models\Category::create(

            [
                'name' => 'Kitchen utensils',
                'store_id' => 3,

            ],
        );
        \App\Models\Category::create(
            [
                'name' => 'Clothes',
                'store_id' => 3,

            ],
        );
    }
}
