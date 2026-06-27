<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TrainingClass */
class ClassResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'location' => $this->location,
            'status' => $this->status,
            'coach_id' => $this->coach_id,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'sessions_count' => $this->whenCounted('sessions'),
            'students_count' => $this->whenCounted('students'),
            'sessions' => SessionResource::collection($this->whenLoaded('sessions')),
            'students' => StudentResource::collection($this->whenLoaded('students')),
            'created_at' => $this->created_at,
        ];
    }
}
