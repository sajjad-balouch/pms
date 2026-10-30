<?php

namespace App\Services;

use App\Models\Plot;
use App\Models\Property;
use App\Models\SystemSetting;
use App\Models\UnlockedContact;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Paid contact unlocks (used by the mobile API).
 * Fee always from admin settings; wallet row locked; never charges twice; creates a lead for the seller.
 * Returns ['status' => unlocked|already|insufficient, 'fee' => float].
 */
class UnlockService
{
    public function unlockProperty(User $buyer, Property $property): array
    {
        $fee = (float) SystemSetting::getSettings()->lead_unlock_fee_agent;

        return DB::transaction(function () use ($buyer, $property, $fee) {
            $user = User::whereKey($buyer->id)->lockForUpdate()->firstOrFail();

            if ((int) $property->agent_id === (int) $user->id
                || DB::table('property_unlocks')->where('user_id', $user->id)->where('property_id', $property->id)->exists()) {
                return ['status' => 'already', 'fee' => 0.0];
            }

            if ((float) $user->wallet_balance < $fee) {
                return ['status' => 'insufficient', 'fee' => $fee];
            }

            $user->decrement('wallet_balance', $fee);

            DB::table('property_unlocks')->insert([
                'user_id' => $user->id,
                'property_id' => $property->id,
                'amount_paid' => $fee,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $fee,
                'type' => 'unlock_fee',
                'status' => 'approved',
                'description' => 'Unlocked contact details for property #' . $property->id . ' (mobile app)',
                'reference_id' => (string) $property->id,
            ]);

            LeadCapture::fromUnlock($property->agent_id, $user, [
                'property_id' => $property->id,
                'town_id' => $property->town_id,
                'title' => $property->title . ' (PKR ' . number_format((float) $property->price) . ')',
            ]);

            return ['status' => 'unlocked', 'fee' => $fee];
        });
    }

    public function unlockPlot(User $buyer, Plot $plot): array
    {
        $fee = (float) SystemSetting::getSettings()->lead_unlock_fee_town;
        $plot->loadMissing('town');
        $ownerId = $plot->town?->user_id;

        return DB::transaction(function () use ($buyer, $plot, $fee, $ownerId) {
            $user = User::whereKey($buyer->id)->lockForUpdate()->firstOrFail();

            $already = (int) $ownerId === (int) $user->id || UnlockedContact::where('user_id', $user->id)
                ->where(fn ($q) => $q->where('plot_id', $plot->id)->orWhere('unlocked_user_id', $ownerId))
                ->where('expires_at', '>', now())
                ->exists();

            if ($already) {
                return ['status' => 'already', 'fee' => 0.0];
            }

            if ((float) $user->wallet_balance < $fee) {
                return ['status' => 'insufficient', 'fee' => $fee];
            }

            $user->decrement('wallet_balance', $fee);

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'unlock_fee',
                'amount' => $fee,
                'status' => 'approved',
                'description' => 'Unlocked contact details for Plot #' . $plot->plot_number . ' (mobile app)',
                'reference_id' => (string) $plot->id,
            ]);

            UnlockedContact::create([
                'user_id' => $user->id,
                'unlocked_user_id' => $ownerId,
                'plot_id' => $plot->id,
                'expires_at' => Carbon::now()->addMonth(),
            ]);

            LeadCapture::fromUnlock($ownerId, $user, [
                'plot_id' => $plot->id,
                'town_id' => $plot->town_id,
                'title' => ($plot->town->name ?? 'Scheme') . ' — Plot #' . $plot->plot_number . ' (' . $plot->size . ')',
            ]);

            return ['status' => 'unlocked', 'fee' => $fee];
        });
    }
}
