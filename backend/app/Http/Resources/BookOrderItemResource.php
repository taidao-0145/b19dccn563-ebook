<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookOrderItemResource extends JsonResource
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
            'price' => $this->price,
            'quantity' => $this->quantity,
            'book' => new BookResource($this->book),
        ];
        if ($this->relationLoaded('book')) {
            $response['book'] = new BookResource($this->book);
        }

        return $response;
    }
}
