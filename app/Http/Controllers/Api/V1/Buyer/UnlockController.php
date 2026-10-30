<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\Transform;
use App\Models\Plot;
use App\Models\Property;
use App\Models\Town;
use App\Models\UnlockedContact;
use App\Services\UnlockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnlockController extends ApiController
{
    public function unlockProperty(Request $request, Property $property, UnlockService $service)
    {
        abort_unless($property->is_active === 'active', 404);

        $result = $service->unlockProperty($request->user(), $property);

        if ($result['status'] === 'insufficient') {
            return $this->fail(__('Insufficient wallet balance. Please top up your wallet in dashboard.'), 402, ['required_amount' => [$result['fee']]]);
        }

        $property->load(['city:id,name', 'town:id,name', 'agent:id,name,agency_name,phone,email']);

        return $this->ok([
            'property' => Transform::property($property, true),
            'charged' => $result['fee'],
            'wallet_balance' => round((float) $request->user()->fresh()->wallet_balance, 2),
        ], $result['status'] === 'already' ? __('This contact is already unlocked.') : __('Contact details unlocked successfully!'));
    }

    public function unlockPlot(Request $request, Plot $plot, UnlockService $service)
    {
        $plot->load('town.user');
        abort_unless($plot->status === 'available' && $plot->town?->is_active, 404);

        $result = $service->unlockPlot($request->user(), $plot);

        if ($result['status'] === 'insufficient') {
            return $this->fail(__('Insufficient wallet balance. Please top up your wallet in dashboard.'), 402, ['required_amount' => [$result['fee']]]);
        }

        $owner = $plot->town->user;

        return $this->ok([
            'plot' => Transform::plot($plot),
            'owner' => ['name' => $owner?->name, 'phone' => $owner?->phone, 'email' => $owner?->email],
            'charged' => $result['fee'],
            'wallet_balance' => round((float) $request->user()->fresh()->wallet_balance, 2),
        ], $result['status'] === 'already' ? __('This contact is already unlocked.') : __('Contact details unlocked successfully!'));
    }

    public function myUnlocks(Request $request)
    {
        $user = $request->user();

        $properties = Property::with(['city:id,name', 'town:id,name', 'agent:id,name,agency_name,phone,email'])
            ->whereIn('id', DB::table('property_unlocks')->where('user_id', $user->id)->select('property_id'))
            ->latest('updated_at')
            ->get()
            ->map(fn ($p) => Transform::property($p, true));

        $owners = UnlockedContact::with('unlockedUser:id,name,phone,email,role')
            ->where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->latest()
            ->get()
            ->map(fn ($u) => [
                'name' => $u->unlockedUser?->name,
                'phone' => $u->unlockedUser?->phone,
                'email' => $u->unlockedUser?->email,
                'role' => $u->unlockedUser?->role,
                'plot_id' => $u->plot_id,
                'expires_at' => Transform::time($u->expires_at),
            ]);

        return $this->ok(['properties' => $properties, 'contacts' => $owners]);
    }

    public function savedProperties(Request $request)
    {
        $page = Property::with(['city:id,name', 'town:id,name', 'agent:id,name,agency_name'])
            ->whereIn('id', DB::table('saved_properties')->where('user_id', $request->user()->id)->select('property_id'))
            ->where('is_active', 'active')
            ->latest('updated_at')
            ->paginate($this->perPage());

        return $this->paginated($page, fn ($p) => Transform::property($p));
    }

    public function toggleSave(Request $request, Property $property)
    {
        $userId = $request->user()->id;
        $query = DB::table('saved_properties')->where('user_id', $userId)->where('property_id', $property->id);

        if ($query->exists()) {
            $query->delete();

            return $this->ok(['is_saved' => false], __('Removed from saved.'));
        }

        DB::table('saved_properties')->insert(['user_id' => $userId, 'property_id' => $property->id, 'created_at' => now(), 'updated_at' => now()]);

        return $this->ok(['is_saved' => true], __('Saved.'));
    }
}
