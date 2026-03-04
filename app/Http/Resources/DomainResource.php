<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    /** @mixin \App\Models\Domain */
    class DomainResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            return [
                'id' => $this->id,
                'domain' => $this->domain,
                'user_id' => $this->user_id,
                'created_at' => optional($this->created_at)->toISOString(),
                'updated_at' => optional($this->updated_at)->toISOString(),
            ];
        }
    }