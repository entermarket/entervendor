<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Product::insert(
            [
                'product_name' => 'Superman Action figure',
                'product_desc' => 'SUperman toy for children',
                'price' => 200000,
                'manufacture_date' => ' 01 / 12 / 2022',
                'expiry_date' => '01/12/2022',
                'product_no' => 183792,
                'batch_no' => 001,
                'in_stock' => 10,
                'category_id' => 1,
                'store_id' => 1
            ],
        );
        \App\Models\Product::insert(
            [
                'product_name' => 'Waw Detergent',
                'product_desc' => 'For washing and so much more',
                'price' => 200,
                'manufacture_date' => ' 01 / 12 / 2022',
                'expiry_date' => '01/12/2022',
                'product_no' => 183792,
                'batch_no' => 001,
                'in_stock' => 20,
                'category_id' => 2,
                'store_id' => 1
            ],
        );
        \App\Models\Product::insert(
            [
                'product_name' => 'Denim Jean',
                'product_desc' => 'Jean for women',
                'price' => 2000,
                'manufacture_date' => ' 01 / 12 / 2022',
                'expiry_date' => '01/12/2022',
                'product_no' => 183792,
                'batch_no' => 001,
                'in_stock' => 15,
                'category_id' => 2,
                'store_id' => 1
            ],
        );
        \App\Models\Product::insert(
            [
                'product_name' => 'Batman Action figure',
                'product_desc' => 'Batman toy for children',
                'price' => 200000,
                'manufacture_date' => ' 01 / 12 / 2022',
                'expiry_date' => '01/12/2022',
                'product_no' => 183792,
                'batch_no' => 001,
                'in_stock' => 30,
                'category_id' => 1,
                'store_id' => 2
            ],
        );
        \App\Models\Product::insert(
            [
                'product_name' => 'Waw detergent',
                'product_desc' => 'For washing and cleaning',
                'price' => 180,
                'manufacture_date' => ' 01 / 12 / 2022',
                'expiry_date' => '01/12/2022',
                'product_no' => 183792,
                'batch_no' => 001,
                'in_stock' => 10,
                'category_id' => 2,
                'store_id' => 2
            ],
        );
        \App\Models\Product::insert(
            [
                'product_name' => 'Denim Jean',
                'product_desc' => 'Jean for women',
                'price' => 2100,
                'manufacture_date' => ' 01 / 12 / 2022',
                'expiry_date' => '01/12/2022',
                'product_no' => 183792,
                'batch_no' => 001,
                'in_stock' => 10,
                'category_id' => 3,
                'store_id' => 2
            ],
        );
        \App\Models\Product::insert(
            [
                'product_name' => 'Washing machine',
                'product_desc' => '200kg washing machine',
                'price' => 250000,
                'manufacture_date' => ' 01 / 12 / 2022',
                'expiry_date' => '01/12/2022',
                'product_no' => 183792,
                'batch_no' => 001,
                'in_stock' => 10,
                'category_id' => 1,
                'store_id' => 3
            ],


        );
    }
}
