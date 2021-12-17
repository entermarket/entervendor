<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','store_id' ,'product_id', 'quantity', 'price', 'brand_name', 'store_name', 'product_name', 'description', 'image', 'subtotal', 'order_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
