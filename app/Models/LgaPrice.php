<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LgaPrice extends Model
{
    use HasFactory;
    protected $fillable = ['lga', 'standard_fee','express_fee', 'scheduled_fee',];
}
