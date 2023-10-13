<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'coin' => $this->coin,
            'price' => $this->price,
            'reduced_price' => $this->reduced_price,
            'status' => $this->status,
            'type_account' => $this->type_account
        ];
    }
}
