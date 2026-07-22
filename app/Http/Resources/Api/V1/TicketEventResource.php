<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\TicketEventType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketEventResource extends JsonResource
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
            'type' => $this->type,
            'payload' => json_decode($this->payload)
        ];
    }
}
