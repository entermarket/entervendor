<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            "id" => $this->id,
            "order_no" => $this->order_no,
            "status" => $this->status,
            "total_amount" => $this->total_amount,
            "commission" => 0,
            "shipping_charges" => 0,
            'weight'=>$this->weight?$this->weight:0,
            "discount" => 0,
            "grand_total" => $this->grand_total,
            "user" => User::find($this->user_id),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "title" => $this->title,
            "isScheduled" => $this->isScheduled?true:false,
            "schedule_time" => $this->isScheduled?$this->schedule_time:false,
            "items" => $this->items,
            "name" => $this->name,
            "payment_status" => $this->payment_status,
            "shipping_method" => $this->shipping_method,
            'orderhistoriesitems' => count($this->orderhistories),
            'orderhistories' => $this->orderhistories,
            'orderinfo' => $this->orderinfo,
            'logistic'=> $this->logistic,
            'logistic_status' => $this->logistic_status

        ];
    }
}
