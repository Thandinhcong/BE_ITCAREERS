<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManagementWebResource extends JsonResource
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
            'logo' => $this->logo,
            'banner' => $this->banner,
            'name_web' => $this->name_web,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'sdt_lienhe' => $this->sdt_lienhe,
        ];
    }
}
