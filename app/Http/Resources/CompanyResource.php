<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'company_name' => $this->company_name,
            'tax_code' => $this->tax_code,
            'address' => $this->address,
            'founded_in' => $this->founded_in,
            'name' => $this->name,
            'office' => $this->office,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'map' => $this->map,
            'logo' => $this->logo,
            'link_web' => $this->link_web,
            'image_paper' => $this->image_paper,
            'desc' => $this->desc,
            'coin' => $this->coin,
            'token' => $this->token,
            'status' => $this->status,
        ];
    }
}
