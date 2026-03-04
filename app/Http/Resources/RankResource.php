<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    /** @mixin \App\Models\Domain */
    class RankResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            return [
                'id' => $this->id,
                'domain_id' => $this->domain_id,
                'domain' => new DomainResource($this->whenLoaded('domain')),
                'location_id' => $this->location_id,
                'location' => new LocationResource($this->whenLoaded('location')),
                'language_id' => $this->language_id,
                'language' => new LanguageResource($this->whenLoaded('language')),

                'keyword' => $this->keyword,

                'rank_min' => $this->rank_min,
                'rank_max' => $this->rank_max,
                'rank_avg' => $this->rank_avg,

                'created_at' => optional($this->created_at)->toISOString(),
                'updated_at' => optional($this->updated_at)->toISOString(),
            ];
        }
    }