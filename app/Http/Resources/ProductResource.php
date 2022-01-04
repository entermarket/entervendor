<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'product_desc' => $this->product_desc,
            'brand_id' => $this->brand_id,
            'price' => $this->price,
            'sales_price' => $this->sales_price,
            'category_id' => $this->category_id,
            'image' => $this->image,
            'store' => $this->store,
            'brand' => $this->brand,
            "manufacture_date" => $this->manufacture_date,
            "expiry_date" => $this->expiry_date,
            "product_no" => $this->product_no,
            "batch_no" => $this->batch_no,
            "in_stock" => $this->in_stock,
        ];
    }
}
