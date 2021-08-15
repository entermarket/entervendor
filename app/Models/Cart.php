<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Cart extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['user_id', 'product_id', 'quantity', 'price', 'total_amount', 'brand_name', 'store_name', 'product_name', 'description', 'image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
