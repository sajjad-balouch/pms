<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Plot;
use App\Models\UnlockedContact;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AvailablePlots extends Component
{
    use WithPagination;

    public $type = 'all';
    public $size = 'all';
    public $search = '';

    public $showModal = false;
    public $selectedPlot = null;
    public $unlockFee = 500;
    public $unlockedContactData = null;

    public function updatedType() { $this->resetPage(); }
    public function updatedSize() { $this->resetPage(); }
    public function updatedSearch() { $this->resetPage(); }

    public function inquirePlot($plotId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Inquire karne ke liye pehle Login/Register karein.');
            return redirect()->route('login');
        }

        // Plot ke sath Town aur Town ka User (Owner) load karein
        $this->selectedPlot = Plot::with(['town.user'])->find($plotId);

        if (!$this->selectedPlot) {
            return;
        }

        // Town se owner ID nikalein
        $ownerId = $this->selectedPlot->town->user_id ?? null;

        // Check karein ke user ne ye plot ya is owner ke contacts pehle se unlock kiye hain ya nahi
        $existing = UnlockedContact::where('user_id', Auth::id())
            ->where(function($q) use ($ownerId) {
                $q->where('plot_id', $this->selectedPlot->id);
                if ($ownerId) {
                    $q->orWhere('unlocked_user_id', $ownerId);
                }
            })
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })
            ->first();

        if ($existing) {
            $owner = $this->selectedPlot->town->user ?? null;
            $this->unlockedContactData = [
                'name'  => $owner->name ?? 'N/A',
                'phone' => $owner->phone ?? 'N/A',
                'email' => $owner->email ?? 'N/A',
            ];
        } else {
            $this->unlockedContactData = null;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPlot = null;
        $this->unlockedContactData = null;
    }

    public function processUnlock()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$this->selectedPlot) {
            session()->flash('modal_error', 'Invalid plot selection.');
            return;
        }

        $owner = $this->selectedPlot->town->user ?? null;
        $ownerId = $owner->id ?? null;

        // Double payment check
        $alreadyUnlocked = UnlockedContact::where('user_id', $user->id)
            ->where(function($q) use ($ownerId) {
                $q->where('plot_id', $this->selectedPlot->id);
                if ($ownerId) {
                    $q->orWhere('unlocked_user_id', $ownerId);
                }
            })
            ->exists();

        if ($alreadyUnlocked) {
            $this->unlockedContactData = [
                'name'  => $owner->name ?? 'N/A',
                'phone' => $owner->phone ?? 'N/A',
                'email' => $owner->email ?? 'N/A',
            ];
            return;
        }

        if (($user->wallet_balance ?? 0) < $this->unlockFee) {
            session()->flash('modal_error', 'Aapke wallet me sufficient balance nahi hai. Pehle wallet top-up karein.');
            return;
        }

        DB::beginTransaction();
        try {
            $user->decrement('wallet_balance', $this->unlockFee);

            WalletTransaction::create([
                'user_id'     => $user->id,
                'type'        => 'unlock_fee',
                'amount'      => $this->unlockFee,
                'description' => "Unlocked contact details for Plot #" . $this->selectedPlot->plot_number,
                'status'      => 'approved',
            ]);

            // Save Plot ID and Owner's User ID explicitly
            UnlockedContact::create([
                'user_id'          => $user->id,
                'unlocked_user_id' => $ownerId,
                'plot_id'          => $this->selectedPlot->id,
                'expires_at'       => Carbon::now()->addMonth(),
            ]);

            DB::commit();

            $this->unlockedContactData = [
                'name'  => $owner->name ?? 'N/A',
                'phone' => $owner->phone ?? 'N/A',
                'email' => $owner->email ?? 'N/A',
            ];

            session()->flash('modal_success', 'Contact details successfully unlock ho chuki hain!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('modal_error', 'Transaction fail ho gayi: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $plots = Plot::with(['town.user'])
            ->where('status', 'available')
            ->when($this->type !== 'all', fn($q) => $q->where('type', $this->type))
            ->when($this->size !== 'all', fn($q) => $q->where('size', $this->size))
            ->when($this->search, fn($q) => $q->where('plot_number', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(12);

        $unlockedPlotIds = [];
        $unlockedUserIds = [];

        if (Auth::check()) {
            $unlockedContacts = UnlockedContact::where('user_id', Auth::id())
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })
                ->get();

            $unlockedPlotIds = $unlockedContacts->pluck('plot_id')->filter()->toArray();
            $unlockedUserIds = $unlockedContacts->pluck('unlocked_user_id')->filter()->toArray();
        }

        return view('livewire.pages.available-plots', [
            'plots'           => $plots,
            'unlockedPlotIds' => $unlockedPlotIds,
            'unlockedUserIds' => $unlockedUserIds,
        ])->layout('layouts.front-app');
    }
}