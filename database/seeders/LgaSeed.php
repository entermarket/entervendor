<?php

namespace Database\Seeders;

use App\Models\Lga;
use App\Models\LgaPrice;
use Illuminate\Database\Seeder;

class LgaSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lgas = [
            [

                "name" => "Agege"
            ],
            [
                "name" => "Agboyi/Ketu"
            ],
            [
                "name" => "Ajeromi-Ifelodun",
                "id" => 1
            ],
            [
                "name" => "Alimosho",
                "id" => 2
            ],
            [
                "name" => "Amuwo-Odofin",
                "id" => 3
            ],
            [
                "name" => "Apapa",
                "id" => 4
            ],
            [
                "name" => "Badagry",
                "id" => 5
            ],
            [
                "name" => "Epe",
                "id" => 6
            ],
            [
                "name" => "Eti Osa",
                "id" => 7
            ],
            [
                "name" => "Ibeju-Lekki",
                "id" => 8
            ],
            [
                "name" => "Ifako-Ijaiye",
                "id" => 9
            ],
            [
                "name" => "Ikeja",
                "id" => 10
            ],
            [
                "name" => "Ikorodu",
                "id" => 11
            ],
            [
                "name" => "Kosofe",
                "id" => 12
            ],
            [
                "name" => "Lagos Island",
                "id" => 13
            ],
            [
                "name" => "Lagos Mainland",
                "id" => 14
            ],
            [
                "name" => "Mushin",
                "id" => 15
            ],
            [
                "name" => "Ojo",
                "id" => 16
            ],
            [
                "name" => "Oshodi-Isolo",
                "id" => 17
            ],
            [
                "name" => "Shomolu",
                "id" => 18
            ],
            [
                "name" => "Surulere",
                "id" => 19
            ],

        ];

        foreach ($lgas as $lga) {
            $alllga = new Lga();
            $alllga->lga = $lga['name'];
            $alllga->save();
        }

        $alllgas = Lga::all();

        foreach ($alllgas as $lga) {
            foreach ($alllgas as $lga1) {

                $lgaprice = new LgaPrice();
                $lgaprice->lga = $lga['lga'];
                $lgaprice->lga_id = $lga['id'];
                $lgaprice->to = $lga1['lga'];
                $lgaprice->to_id = $lga1['id'];
                $lgaprice->standard_fee = 1000;
                $lgaprice->express_fee = 1500;
                $lgaprice->scheduled_fee = 1200;
                $lgaprice->save();
            }
        }
    }
}
