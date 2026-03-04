<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    /** @mixin \App\Models\Domain */
    class LanguageResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            return [
                'id' => $this->id,
                'language_code' => $this->language_code,
                'language_name' => $this->language_name,
                'created_at' => optional($this->created_at)->toISOString(),
                'updated_at' => optional($this->updated_at)->toISOString(),
            ];
        }
    }