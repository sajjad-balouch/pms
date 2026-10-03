<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentMethod;
use App\Models\WalletTransaction;
use App\Models\Property;
use App\Models\UnlockedContact;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserDashboard extends Component
{
    use WithFileUploads;

    public $activeTab = 'overview';
    
    // Top-up Modal state
    public bool $showTopUpModal = false;

    public $topUpAmount = 1000;
    public $selectedMethodId = null;
    public $senderAccountTitle = '';
    public $senderAccountNumber = '';
    public $screenshot;

    public function openTopUpModal()
    {
        $this->resetErrorBag();
        $this->showTopUpModal = true;
    }

    public function closeTopUpModal()
    {
        $this->showTopUpModal = false;
    }

    public function depositWallet()
    {
        $this->validate([
            'topUpAmount' => 'required|numeric|min:100|max:500000',
            'selectedMethodId' => 'required|exists:payment_methods,id',
            'senderAccountTitle' => 'required|string|max:255',
            'senderAccountNumber' => 'required|string|max:255',
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $method = PaymentMethod::findOrFail($this->selectedMethodId);
        $destinationPath = public_path('ss');
        $imageName = time() . '_' . uniqid() . '.' . $this->screenshot->getClientOriginalExtension();
        copy($this->screenshot->getRealPath(), $destinationPath . '/' . $imageName);

        WalletTransaction::create([
            'user_id' => Auth::id(),
            'amount' => $this->topUpAmount,
            'type' => 'deposit',
            'payment_method' => $method->method_name,
            'sender_account_title' => $this->senderAccountTitle,
            'sender_account_number' => $this->senderAccountNumber,
            'screenshot' => 'ss/' . $imageName,
            'status' => 'pending',
            'description' => 'Deposit via ' . $method->method_name . ' (Awaiting Admin Approval)',
        ]);

        $this->showTopUpModal = false;
        $this->reset(['selectedMethodId', 'senderAccountTitle', 'senderAccountNumber', 'screenshot']);
        
        session()->flash('success', 'Your top-up request has been submitted successfully!');
    }

    public function render()
    {
        $user = Auth::user();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        // 1. Unlocked Properties
        $unlockedPropertyIds = DB::table('property_unlocks')
            ->where('user_id', $user->id)
            ->pluck('property_id');

        $unlockedProperties = Property::with(['agent', 'town'])
            ->whereIn('id', $unlockedPropertyIds)
            ->latest()
            ->get();

        // 2. Unlocked Contacts (Agents & Town Owners with 1-Month Validity)
        $unlockedContacts = UnlockedContact::with('unlockedUser')
            ->where('user_id', $user->id)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->get();

        // 3. Transactions
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(15)
            ->get();

        return view('livewire.user-dashboard', [
            'user'               => $user,
            'paymentMethods'     => $paymentMethods,
            'unlockedProperties' => $unlockedProperties,
            'unlockedContacts'   => $unlockedContacts,
            'transactions'       => $transactions,
        ])->layout('layouts.front-app');
    }
}