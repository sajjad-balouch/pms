<?php

namespace App\Http\Controllers\Api\V1\Agent;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\Transform;
use App\Models\City;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AgentPropertyController extends ApiController
{
    const MAX_IMAGES = 15;

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $page = $this->owned($request)
            ->with(['city:id,name', 'town:id,name'])
            ->select('properties.*')
            ->selectSub(DB::table('property_unlocks')->selectRaw('COUNT(*)')->whereColumn('property_unlocks.property_id', 'properties.id'), 'unlocks_count')
            ->selectSub(DB::table('leads')->selectRaw('COUNT(*)')->whereColumn('leads.property_id', 'properties.id'), 'leads_count')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('type'), fn ($q) => $q->where('property_type', $request->query('type')))
            ->when($request->filled('purpose'), fn ($q) => $q->where('purpose', $request->query('purpose')))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('location', 'like', "%{$search}%")))
            ->latest('updated_at')
            ->paginate($this->perPage());

        return $this->paginated($page, fn ($p) => $this->present($p));
    }

    public function show(Request $request, Property $property)
    {
        $this->authorizeOwn($request, $property);
        $property->load(['city:id,name', 'town:id,name']);

        return $this->ok($this->present($property));
    }

    // multipart/form-data, images[] optional
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $images = [];
        foreach ((array) $request->file('images', []) as $file) {
            $images[] = $this->storePublicImage($file, 'property_images', 'prop');
        }

        $property = Property::create($data + [
            'agent_id' => $request->user()->id,
            'images' => $images,
            // is_active left empty, same as the website: admin moderation makes it public
        ] + $this->legacyCityColumn($data['city_id']));

        $property->load(['city:id,name', 'town:id,name']);

        return $this->created($this->present($property), __('New property listed successfully.'));
    }

    // POST (multipart) so the app can send new images[] with the edit
    public function update(Request $request, Property $property)
    {
        $this->authorizeOwn($request, $property);
        $data = $this->validated($request, $property);

        $images = array_values((array) $property->images);
        foreach ((array) $request->file('images', []) as $file) {
            $images[] = $this->storePublicImage($file, 'property_images', 'prop');
        }

        $property->update($data + ['images' => $images] + $this->legacyCityColumn($data['city_id']));
        $property->load(['city:id,name', 'town:id,name']);

        return $this->ok($this->present($property), __('Property updated successfully.'));
    }

    public function deleteImage(Request $request, Property $property, int $index)
    {
        $this->authorizeOwn($request, $property);

        $images = array_values((array) $property->images);
        abort_unless(isset($images[$index]), 404);

        $this->deletePublicFile($images[$index], 'property_images/');
        unset($images[$index]);
        $property->update(['images' => array_values($images)]);

        return $this->ok($this->present($property->fresh(['city:id,name', 'town:id,name'])), __('Photo deleted.'));
    }

    public function refresh(Request $request, Property $property)
    {
        $this->authorizeOwn($request, $property);
        $property->touch();

        return $this->ok(null, __('Listing refreshed.'));
    }

    public function destroy(Request $request, Property $property)
    {
        $this->authorizeOwn($request, $property);

        foreach ((array) $property->images as $img) {
            $this->deletePublicFile($img, 'property_images/');
        }
        $property->delete();

        return $this->ok(null, __('Property deleted.'));
    }

    protected function validated(Request $request, ?Property $property = null): array
    {
        $existing = $property ? count((array) $property->images) : 0;

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'property_type' => 'required|in:residential,commercial,agricultural,shop,house,apartment,plot',
            'purpose' => 'required|in:for_sale,for_rent',
            'price' => 'required|numeric|min:0',
            'area_size' => 'required|string|max:50',
            'city_id' => 'required|exists:cities,id',
            'town_id' => 'nullable|exists:towns,id',
            'location' => 'required|string|max:255',
            'google_map_url' => 'nullable|url|max:500',
            'description' => 'nullable|string|max:5000',
            'status' => 'nullable|in:available,under_offer,sold,rented',
            'images' => 'nullable|array|max:' . max(0, self::MAX_IMAGES - $existing),
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ], ['images.max' => __('A listing can have at most :max photos.', ['max' => self::MAX_IMAGES])]);

        unset($data['images']);
        $data['status'] = $data['status'] ?? 'available';

        return $data;
    }

    // Older installs still have the required "city" text column on properties
    protected function legacyCityColumn($cityId): array
    {
        static $hasCity;
        $hasCity ??= Schema::hasColumn('properties', 'city');

        return $hasCity ? ['city' => City::find($cityId)?->name ?? ''] : [];
    }

    protected function owned(Request $request)
    {
        return Property::where('agent_id', $request->user()->id);
    }

    protected function authorizeOwn(Request $request, Property $property): void
    {
        abort_unless((int) $property->agent_id === (int) $request->user()->id, 404);
    }

    protected function present(Property $p): array
    {
        return Transform::property($p, false) + [
            'moderation_status' => $p->is_active, // active | pending | banned ...
            'rejection_reason' => $p->rejection_reason,
            'unlocks_count' => (int) ($p->unlocks_count ?? DB::table('property_unlocks')->where('property_id', $p->id)->count()),
            'leads_count' => (int) ($p->leads_count ?? DB::table('leads')->where('property_id', $p->id)->count()),
            'is_stale' => $p->status === 'available' && $p->updated_at && $p->updated_at->lt(now()->subDays(30)),
        ];
    }
}
