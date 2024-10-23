<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'token' => $this->token ?? '',       
            'created_at' => $this->created_at?->toDateTimeString() ?? '',
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : ""
        ];
    }
}
