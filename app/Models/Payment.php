<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'amount', 'service', 'user_id', 'reference', 'status', 'netowrk', 'number', 'service_id', 'token', 'transactionRef', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $hidden = [
        'token'
    ];
}
