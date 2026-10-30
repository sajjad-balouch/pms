<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Plot;
use App\Models\Town;
use App\Support\Tenant;

/**
 * Base for town-owner endpoints: everything is scoped to the owner's account
 * (team members work inside their owner's account).
 */
abstract class OwnerController extends ApiController
{
    protected function ownerId(): int
    {
        return (int) Tenant::ownerId();
    }

    protected function ownerTowns()
    {
        return Town::where('user_id', $this->ownerId());
    }

    protected function ownedTown($id): Town
    {
        return $this->ownerTowns()->findOrFail($id);
    }

    protected function ownerPlots()
    {
        return Plot::whereIn('town_id', $this->ownerTowns()->select('id'));
    }

    protected function ownedPlot($id): Plot
    {
        return $this->ownerPlots()->findOrFail($id);
    }

    protected function ensureTownOwned(int $townId): void
    {
        abort_unless($this->ownerTowns()->whereKey($townId)->exists(), 404);
    }
}
