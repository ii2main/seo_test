<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    /** @mixin \App\Models\Domain */
    class LocationResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            return [
                'id' => $this->id,
                'location_code' => $this->location_code,
                'location_name' => $this->location_name,
                'location_code_parent' => $this->location_code_parent,
                'country_iso_code' => $this->country_iso_code,
                'location_type' => $this->location_type,
                'created_at' => optional($this->created_at)->toISOString(),
                'updated_at' => optional($this->updated_at)->toISOString(),
            ];
        }
    }