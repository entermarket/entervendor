<?php

namespace App\Services;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Support\Collection;
use Illuminate\Support\Facades\Hash;

class StoreService
{
  public function createstore($request)
  {
    if (!count($request->images)) return response('Upload an image', 422);
    return  Store::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'location'  => $request->location,
      'image'  => $request->images[0]['preview'],
      'connection'  => 'null',
      'api_endpoint'  => 'null',
      'db_host'  => 'null',
      'db_username'  => $request->db_username,
      'db_password'  => $request->db_password
    ]);
  }
  public function findStoreById()
  {
  }

  public function getallstores($request)
  {


    $stores = Store::latest()->get();
    $result = $stores->map(function ($a) use ($request) {
      $a['distance'] = $this->distanceCalculation($request['lat'], $request['long'], $a['lat'], $a['long']);
      return $a;
    })->sortBy(function ($a) {
      return $a['distance'] ;
    });

    return  StoreResource::collection($result->values()->paginate(30));
  }
  public function showallstores()
  {

   return StoreResource::collection(Store::latest()->paginate(30));
  }
  public  function distanceCalculation($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'mi', $decimals = 2)
  {
    // Calculate the distance in degrees
    $degrees = rad2deg(acos((sin(deg2rad($point1_lat)) * sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat)) * cos(deg2rad($point2_lat)) * cos(deg2rad($point1_long - $point2_long)))));

    // Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
    switch ($unit) {
      case 'km':
        $distance = $degrees * 111.13384; // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
        break;
      case 'mi':
        $distance = $degrees * 69.05482; // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
        break;
      case 'nmi':
        $distance =  $degrees * 59.97662; // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
    }
    return round($distance, $decimals);
  }
}
