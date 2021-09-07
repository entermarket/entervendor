<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'location',
        'image',
        'connection',
        'api_endpoint',
        'db_host',
        'db_username',
        'db_password'
    ];
}
