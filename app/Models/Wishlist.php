<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'store_id', 'store_name', 'product_name', 'product_desc', 'user_id'];

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
