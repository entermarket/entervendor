<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StoreSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Store::create(
            [
                'name' => 'Hubmart',
                'location' => 'lekki',
                'image' => '/static/media/hubmart.663e09e1.png',
                'connection' => 'api',
                'api_endpoint' => '',
                'db_host' => '',
                'db_username' => '',
                'db_password' => ''
            ],
        );
        \App\Models\Store::create(
            [
                'name' => 'Spar',
                'location' => 'ikoyi',
                'image' => '/static/media/hubmart.663e09e1.png',
                'connection' => 'api',
                'api_endpoint' => '',
                'db_host' => '',
                'db_username' => '',
                'db_password' => ''
            ]
        );
        \App\Models\Store::create(
            [
                'name' => 'Shoprite',
                'location' => 'ikeja',
                'image' => '/static/media/hubmart.663e09e1.png',
                'connection' => 'api',
                'api_endpoint' => '',
                'db_host' => '',
                'db_username' => '',
                'db_password' => ''
            ],
        );
    }
}
