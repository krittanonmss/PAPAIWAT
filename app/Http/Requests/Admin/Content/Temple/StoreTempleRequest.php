<?php

namespace App\Http\Requests\Admin\Content\Temple;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTempleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_popular' => $this->boolean('is_popular'),
            'cover_media_id' => $this->integerOrNull($this->input('cover_media_id')),
            'gallery_media_ids' => collect($this->input('gallery_media_ids', []))
                ->filter(fn ($id) => is_scalar($id) && preg_match('/^\d+$/', (string) $id))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    private function integerOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_scalar($value) && preg_match('/^\d+$/', (string) $value)
            ? (int) $value
            : null;
    }

    public function rules(): array
    {
        return [
            // content
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contents', 'slug')->where(
                    fn ($query) => $query->where('content_type', 'temple')
                ),
            ],
            'template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('status', 'active')
                        ->where('view_path', 'like', 'frontend.templates.details.%')
                        ->where(function ($query) {
                            $query->where('key', 'temple-detail')
                                ->orWhere('view_path', 'like', 'frontend.templates.details.temple-%');
                        });
                }),
            ],
            'excerpt' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'is_featured' => ['nullable', 'boolean'],
            'is_popular' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],

            // temple
            'temple_type' => ['nullable', 'string', 'max:50'],
            'sect' => ['nullable', 'string', 'max:100'],
            'architecture_style' => ['nullable', 'string', 'max:150'],
            'founded_year' => ['nullable', 'string', 'max:20'],
            'history' => ['nullable', 'string'],
            'dress_code' => ['nullable', 'string'],
            'recommended_visit_start_time' => ['nullable', 'date_format:H:i'],
            'recommended_visit_end_time' => ['nullable', 'date_format:H:i', 'after:recommended_visit_start_time'],

            // address
            'address.address_line' => ['nullable', 'string'],
            'address.province' => ['nullable', 'string', 'max:100'],
            'address.district' => ['nullable', 'string', 'max:100'],
            'address.subdistrict' => ['nullable', 'string', 'max:100'],
            'address.postal_code' => ['nullable', 'string', 'max:20'],
            'address.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'address.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address.google_place_id' => ['nullable', 'string', 'max:255'],
            'address.google_maps_url' => ['nullable', 'string', 'max:255'],

            // categories
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'primary_category_id' => ['nullable', 'integer', 'exists:categories,id'],

            // media
            'cover_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'gallery_media_ids' => ['nullable', 'array'],
            'gallery_media_ids.*' => ['integer', 'exists:media,id'],

            // opening hours
            'opening_hours' => ['nullable', 'array'],
            'opening_hours.*.day_of_week' => ['required_with:opening_hours', 'integer', 'min:0', 'max:6'],
            'opening_hours.*.open_time' => ['nullable', 'date_format:H:i'],
            'opening_hours.*.close_time' => ['nullable', 'date_format:H:i'],
            'opening_hours.*.is_closed' => ['nullable', 'boolean'],
            'opening_hours.*.note' => ['nullable', 'string', 'max:255'],

            // fees
            'fees' => ['nullable', 'array'],
            'fees.*.fee_type' => ['required_with:fees', 'string', 'max:50'],
            'fees.*.label' => ['required_with:fees', 'string', 'max:150'],
            'fees.*.amount' => ['nullable', 'numeric', 'min:0'],
            'fees.*.currency' => ['nullable', 'string', 'max:10'],
            'fees.*.note' => ['nullable', 'string', 'max:255'],
            'fees.*.is_active' => ['nullable', 'boolean'],
            'fees.*.sort_order' => ['nullable', 'integer', 'min:0'],

            // facilities
            'facility_items' => ['nullable', 'array'],
            'facility_items.*.facility_id' => ['nullable', 'integer', 'exists:facilities,id'],
            'facility_items.*.facility_name' => ['nullable', 'string', 'max:255'],
            'facility_items.*.value' => ['nullable', 'string', 'max:255'],
            'facility_items.*.note' => ['nullable', 'string', 'max:255'],
            'facility_items.*.sort_order' => ['nullable', 'integer', 'min:0'],

            // highlights
            'highlights' => ['nullable', 'array'],
            'highlights.*.title' => ['required_with:highlights', 'string', 'max:255'],
            'highlights.*.description' => ['nullable', 'string'],
            'highlights.*.sort_order' => ['nullable', 'integer', 'min:0'],

            // visit rules
            'visit_rules' => ['nullable', 'array'],
            'visit_rules.*.rule_text' => ['required_with:visit_rules', 'string'],
            'visit_rules.*.sort_order' => ['nullable', 'integer', 'min:0'],

            // travel infos
            'travel_infos' => ['nullable', 'array'],
            'travel_infos.*.travel_type' => ['required_with:travel_infos', 'string', 'max:50'],
            'travel_infos.*.start_place' => ['nullable', 'string', 'max:255'],
            'travel_infos.*.distance_km' => ['nullable', 'numeric', 'min:0'],
            'travel_infos.*.duration_minutes' => ['nullable', 'integer', 'min:0'],
            'travel_infos.*.cost_estimate' => ['nullable', 'numeric', 'min:0'],
            'travel_infos.*.note' => ['nullable', 'string'],
            'travel_infos.*.is_active' => ['nullable', 'boolean'],
            'travel_infos.*.sort_order' => ['nullable', 'integer', 'min:0'],

            // nearby places
            'nearby_places' => ['nullable', 'array'],
            'nearby_places.*.nearby_temple_id' => ['required_with:nearby_places', 'integer', 'exists:temples,id', 'different:temple_id'],
            'nearby_places.*.relation_type' => ['nullable', 'string', 'max:50'],
            'nearby_places.*.distance_km' => ['nullable', 'numeric', 'min:0'],
            'nearby_places.*.duration_minutes' => ['nullable', 'integer', 'min:0'],
            'nearby_places.*.score' => ['nullable', 'numeric', 'min:0'],
            'nearby_places.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
