<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'isbn' => $this->isbn,
            'hash_id' => $this->hash_id,
            'c_code' => $this->c_code,
            'name' => $this->name,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'author_name' => $this->author_name,
            'page_number' => $this->page_number,
            'public_date' => $this->public_date,
            'inventory_number' => $this->inventory_number,
            'price' => $this->price,
            'sample_path' => $this->sample_path,
            'book_type' => $this->book_type,
            'is_purchased' => false,
        ];
    }
}
