<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->title,
            'location' => 'lekki',
            'image' => '/static/media/hubmart.663e09e1.png',
            'connection' => 'api',
            'api_end_point' => '',
            'db_host' => '',
            'db_username' => '',
            'db_password' => ''
        ];
    }
}
