<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transaction extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'reference',
        'message',
        'trxref',
        'amount',
        'type',
        'mode',
        'status',
        'content',
        'user_id',
        'order_id'

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','order_no')->with('orderinfo', 'orderhistories');
    }
}
