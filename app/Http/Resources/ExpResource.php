<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpResource extends JsonResource
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
            'postion' => $this->postion,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'major_id' => $this->major_id,
        ];
    }
}
