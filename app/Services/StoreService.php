<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\Hash;

class StoreService
{
  public function createstore($request)
  {
  if( !count($request->images)) return response('Upload an image', 422);
    return  Store::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'location'  => $request->location,
      'image'  => $request->images[0]['preview'],
      'connection'  =>'null',
      'api_endpoint'  => 'null',
      'db_host'  => 'null',
      'db_username'  => $request->db_username,
      'db_password'  => $request->db_password
    ]);
  }
  public function findStoreById()
  {
  }
  public function showallstores()
  {
    return Store::all();
  }
}
