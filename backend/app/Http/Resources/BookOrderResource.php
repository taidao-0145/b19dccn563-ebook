<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = [
            'hash_id' => $this->hash_id,
            'code' => $this->code,
            'total_price' => $this->total_price,
            'shipping_fee' => $this->shipping_fee,
            'deferred_fee' => $this->deferred_fee,
            'discount_percent' => $this->discount_percent,
            'discount_amount' => $this->discount_amount,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'receiver_address' => $this->receiver_address,
            'memo' => $this->memo,
            'shipping_status' => $this->shipping_status,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
        if ($this->relationLoaded('bookOrderItems')) {
            $response['book_order_items'] = BookOrderItemResource::collection($this->bookOrderItems);
        }

        return $response;
    }
}
