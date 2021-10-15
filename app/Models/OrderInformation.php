<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstName',
        'lastName',
        'shipping_method',
        'shipping_address',
        'pickup_location',
        'city',
        'state',
        'email',
        'phoneNumber',
        'extra_instruction',
        'payment_method',
        'user_id',
        'order_id',
        'delivery_method'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
