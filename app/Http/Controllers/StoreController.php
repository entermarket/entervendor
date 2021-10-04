<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Services\StoreService;

class StoreController extends Controller
{
    public $storeservice;
    public $user;

    public function __construct(StoreService $storeservice)
    {
        $this->storeservice = $storeservice;
        $this->user = auth('api')->user();
    }
    public function index()
    {
        return $this->storeservice->showallstores();
    }

    public function store(Request $request)
    {
        return $this->storeservice->createstore($request);
    }

    public function show(Store $store)
    {
        return $store;
    }

    public function getstorecategories(Store $store)
    {
        return $store->categories()->get();
    }
    public function destroy(Store $store)
    {
        $store->delete();
        return $this->response_success('store removed');
    }
}
