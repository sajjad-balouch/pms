<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\City;
use App\Models\Plot;
use App\Models\Property;
use App\Models\SystemSetting;
use App\Models\Town;
use App\Support\PlotPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PublicController extends ApiController
{
    // Everything the app needs to build its filters and price labels
    public function settings()
    {
        $s = SystemSetting::getSettings();

        return $this->ok([
            'app_name' => config('app.name'),
            'currency' => 'PKR',
            'languages' => ['en', 'ur'],
            'fees' => [
                'property_contact_unlock' => (float) $s->lead_unlock_fee_agent,
                'plot_owner_contact_unlock' => (float) $s->lead_unlock_fee_town,
                'min_top_up' => 100,
                'max_top_up' => 500000,
            ],
            'property_types' => ['house', 'plot', 'apartment', 'shop', 'commercial', 'residential', 'agricultural'],
            'property_purposes' => ['for_sale', 'for_rent'],
            'property_statuses' => ['available', 'under_offer', 'sold', 'rented'],
            'plot_sizes' => PlotPricing::SIZES,
            'plot_features' => collect(PlotPricing::FEATURES)->map(fn ($f, $k) => ['key' => $k, 'label' => $f['label'], 'premium_percent' => $f['premium']])->values(),
            'lead_statuses' => ['new', 'contacted', 'site_visit', 'negotiation', 'closed_won', 'closed_lost'],
            'lead_priorities' => ['hot', 'warm', 'cold'],
            'lead_sources' => ['portal', 'whatsapp', 'call', 'walk_in', 'social_media', 'banner'],
        ]);
    }

    public function cities()
    {
        $cities = City::where('is_active', true)
            ->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name')
            ->get();

        return $this->ok($cities->map(fn ($c) => Transform::city($c)));
    }

    public function schemes(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $page = Town::where('is_active', true)
            ->withCount(['plots', 'plots as available_plots_count' => fn ($q) => $q->where('status', 'available')])
            ->when($request->filled('city_id'), fn ($q) => $q->where('city_id', $request->integer('city_id')))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('location', 'like', "%{$search}%")))
            ->orderByDesc('available_plots_count')
            ->paginate($this->perPage());

        return $this->paginated($page, fn ($t) => Transform::town($t, [
            'plots_count' => (int) $t->plots_count,
            'available_plots_count' => (int) $t->available_plots_count,
        ]));
    }

    public function scheme(Town $town)
    {
        abort_unless($town->is_active, 404);

        $stats = Plot::where('town_id', $town->id)->where('status', 'available')
            ->selectRaw('COUNT(*) AS n, MIN(total_price) AS min_price, MAX(total_price) AS max_price')
            ->first();

        return $this->ok(Transform::town($town, [
            'available_plots_count' => (int) $stats->n,
            'price_from' => $stats->min_price !== null ? (float) $stats->min_price : null,
            'price_to' => $stats->max_price !== null ? (float) $stats->max_price : null,
            'available_sizes' => Plot::where('town_id', $town->id)->where('status', 'available')->distinct()->orderBy('size')->pluck('size'),
            'blocks' => Plot::where('town_id', $town->id)->whereNotNull('block_name')->distinct()->orderBy('block_name')->pluck('block_name'),
        ]));
    }

    // Available plots of active schemes (owner contact needs a paid unlock: POST /plots/{id}/unlock)
    public function plots(Request $request)
    {
        $page = Plot::with('town:id,name,city')
            ->where('status', 'available')
            ->whereIn('town_id', Town::where('is_active', true)->select('id'))
            ->when($request->filled('town_id'), fn ($q) => $q->where('town_id', $request->integer('town_id')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->query('type')))
            ->when($request->filled('size'), fn ($q) => $q->where('size', $request->query('size')))
            ->when($request->filled('block'), fn ($q) => $q->where('block_name', $request->query('block')))
            ->when($request->filled('min_price'), fn ($q) => $q->where('total_price', '>=', (float) $request->query('min_price')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('total_price', '<=', (float) $request->query('max_price')))
            ->orderBy('town_id')->orderBy('block_name')->orderByRaw('LENGTH(plot_number)')->orderBy('plot_number')
            ->paginate($this->perPage());

        return $this->paginated($page, fn ($p) => Transform::plot($p));
    }

    public function properties(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $sort = $request->query('sort', 'latest');

        $page = Property::with(['city:id,name', 'town:id,name', 'agent:id,name,agency_name'])
            ->where('is_active', 'active')
            ->when(! $request->boolean('include_sold'), fn ($q) => $q->whereIn('status', ['available', 'under_offer']))
            ->when($request->filled('city_id'), fn ($q) => $q->where('city_id', $request->integer('city_id')))
            ->when($request->filled('town_id'), fn ($q) => $q->where('town_id', $request->integer('town_id')))
            ->when($request->filled('type'), fn ($q) => $q->where('property_type', $request->query('type')))
            ->when($request->filled('purpose'), fn ($q) => $q->where('purpose', $request->query('purpose')))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', (float) $request->query('min_price')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', (float) $request->query('max_price')))
            ->when($request->filled('agent_id'), fn ($q) => $q->where('agent_id', $request->integer('agent_id')))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('location', 'like', "%{$search}%")))
            ->orderByDesc('is_featured')
            ->when($sort === 'price_asc', fn ($q) => $q->orderBy('price'))
            ->when($sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'popular', fn ($q) => $q->orderByDesc('views_count'))
            ->latest('updated_at')
            ->paginate($this->perPage());

        $unlocked = $this->unlockedPropertyIds($request, collect($page->items())->pluck('id'));

        return $this->paginated($page, fn ($p) => Transform::property($p, $this->canSeeContact($request, $p, $unlocked)));
    }

    public function property(Request $request, Property $property)
    {
        $user = $request->user();
        $isOwnListing = $user && (int) $user->id === (int) $property->agent_id;

        abort_unless($property->is_active === 'active' || $isOwnListing, 404);

        $property->load(['city:id,name', 'town:id,name', 'agent:id,name,agency_name,phone,email']);

        // Listing analytics: one view per viewer per 6 hours (the agent's own views are not counted)
        if (! $isOwnListing && Cache::add('api_view:' . $property->id . ':' . ($user?->id ?? $request->ip()), 1, now()->addHours(6))) {
            Property::whereKey($property->id)->update(['views_count' => DB::raw('views_count + 1'), 'last_viewed_at' => now()]);
        }

        $unlocked = $this->unlockedPropertyIds($request, collect([$property->id]));
        $data = Transform::property($property, $this->canSeeContact($request, $property, $unlocked));
        $data['is_saved'] = $user ? DB::table('saved_properties')->where('user_id', $user->id)->where('property_id', $property->id)->exists() : false;
        $data['unlock_fee'] = (float) SystemSetting::getSettings()->lead_unlock_fee_agent;

        return $this->ok($data);
    }

    protected function unlockedPropertyIds(Request $request, $ids)
    {
        $user = $request->user();

        return $user && $ids->isNotEmpty()
            ? DB::table('property_unlocks')->where('user_id', $user->id)->whereIn('property_id', $ids)->pluck('property_id')->flip()
            : collect();
    }

    protected function canSeeContact(Request $request, $property, $unlocked): bool
    {
        $user = $request->user();

        return $user && ((int) $user->id === (int) $property->agent_id || $user->role === 'admin' || $unlocked->has($property->id));
    }
}
