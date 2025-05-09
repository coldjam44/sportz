<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SportsuserControllerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,

            'image' => url('sportsusers/' . $this->image),
        ];
    }
}
