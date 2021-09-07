<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public $wishlistservice;
    public $user;

    public function __construct(WishlistService $wishlistservice)
    {
        $this->wishlistservice = $wishlistservice;
        $this->user = auth('api')->user();
    }

    public function store(Request $request)
    {
        return  $this->wishlistservice->createwishlist($request, $this->user);
    }
    public function index()
    {
        return $this->wishlistservice->userwishlist($this->user);
    }
    public function destroy(Wishlist $wishlist)
    {
        $wishlist->delete();
        return $this->response_success('removed');
    }

    public function destroyall()
    {
        $this->user->wishlist()->delete();
        return $this->response_success('wishlist cleared');
    }
}
