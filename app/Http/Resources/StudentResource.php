<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Student */
class StudentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'photo_url' => $this->photo_url,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'gender' => $this->gender,
            'parent' => [
                'name' => $this->parent_name,
                'email' => $this->parent_email,
                'phone' => $this->parent_phone,
            ],
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            // Pivot data when loaded through a class relationship.
            'enrollment' => $this->whenPivotLoaded('enrollments', fn () => [
                'enrolled_at' => $this->pivot->enrolled_at,
                'status' => $this->pivot->status,
            ]),
            'classes_count' => $this->whenCounted('classes'),
        ];
    }
}
