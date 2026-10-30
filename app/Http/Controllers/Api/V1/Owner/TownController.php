<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Api\V1\Transform;
use App\Models\PlotBooking;
use Illuminate\Http\Request;

class TownController extends OwnerController
{
    public function index()
    {
        $towns = $this->ownerTowns()
            ->withCount([
                'plots',
                'plots as available_plots_count' => fn ($q) => $q->where('status', 'available'),
                'plots as booked_plots_count' => fn ($q) => $q->where('status', 'booked'),
                'plots as sold_plots_count' => fn ($q) => $q->where('status', 'sold'),
            ])
            ->orderBy('name')
            ->get();

        return $this->ok($towns->map(fn ($t) => Transform::town($t, [
            'plots_count' => (int) $t->plots_count,
            'available_plots_count' => (int) $t->available_plots_count,
            'booked_plots_count' => (int) $t->booked_plots_count,
            'sold_plots_count' => (int) $t->sold_plots_count,
        ])));
    }

    public function show($town)
    {
        $town = $this->ownedTown($town);

        return $this->ok(Transform::town($town, [
            'blocks' => $town->plots()->whereNotNull('block_name')->distinct()->orderBy('block_name')->pluck('block_name'),
        ]));
    }

    public function plots(Request $request, $town)
    {
        $town = $this->ownedTown($town);
        $search = trim((string) $request->query('search'));

        $page = $town->plots()
            ->with('customer:id,name,phone')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('block'), fn ($q) => $request->query('block') === '__none'
                ? $q->whereNull('block_name')
                : $q->where('block_name', $request->query('block')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->query('type')))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('plot_number', 'like', "%{$search}%")
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))))
            ->orderBy('block_name')->orderByRaw('LENGTH(plot_number)')->orderBy('plot_number')
            ->paginate($this->perPage(50));

        $bookings = PlotBooking::whereIn('plot_id', collect($page->items())->pluck('id'))->where('status', 'active')->pluck('id', 'plot_id');

        $counts = $town->plots()->selectRaw('status, COUNT(*) AS n')->groupBy('status')->pluck('n', 'status');

        return $this->paginated($page, fn ($p) => Transform::plot($p, true) + ['booking_id' => $bookings[$p->id] ?? null], ['counts' => [
            'available' => (int) ($counts['available'] ?? 0),
            'booked' => (int) ($counts['booked'] ?? 0),
            'sold' => (int) ($counts['sold'] ?? 0),
        ]]);
    }
}
