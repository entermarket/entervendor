<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Product extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'weight',
        'product_name',
        'product_desc',
        'price',
        'manufacture_date',
        'expiry_date',
        'product_no',
        'batch_no',
        'in_stock',
        'category_id',
        'brand_id',
        'store_id',
        'image',
        'sales_price',
        'active'
    ];

    protected $casts = [
        'image' => 'array'
    ];

function save(array $options = array()) {
   if (empty($this->product_no)) {
      $this->product_no = rand(0000000,999999);
   }
        if (empty($this->sales_price)) {
            $this->sales_price = 0;
        }
   return parent::save($options);
}

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
