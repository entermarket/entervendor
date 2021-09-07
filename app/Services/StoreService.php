<?php

namespace App\Services;

use App\Models\Store;

class StoreService
{
  public function createstore($request)
  {
    return  Store::create([
      'name' => $request->name,
      'location'  => $request->location,
      'image'  => $request->image,
      'connection'  => $request->connection,
      'api_endpoint'  => $request->api_endpoint,
      'db_host'  => $request->db_host,
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
