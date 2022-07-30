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
                'name' => 'Others',

            ]
        );
    }
}
