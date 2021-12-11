<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOrder extends Model
{
    use HasFactory;

    protected $fillable = ['quantity','price','subtotal','user_id', 'store_id', 'product_id','order_no','payment_status'];
    public function store(){
        return $this->belongsTo(Store::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
