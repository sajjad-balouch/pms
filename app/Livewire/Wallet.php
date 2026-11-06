<?php

namespace App\Livewire;

use App\Models\MessageLog;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Messaging\Messenger;
use App\Services\Messaging\SmsGateway;
use App\Support\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Agent aur town owner ka wallet: balance, top-up ki darkhwast (screenshot ke saath) aur hisaab.
 * Admin "Top-Ups" page se manzoor karta hai, tab balance barhta hai. SMS ke paise isi wallet se katte hain.
 * Town owner ka staff wallet nahi kholta (route 'team' section mein hai: sirf owner).
 */
class Wallet extends Component
{
    use WithFileUploads, WithPagination;

    public const MIN = 100;

    public const MAX = 500000;

    /** Ek waqt mein itni zyada pending darkhwastein nahi */
    public const MAX_PENDING = 3;

    public $amount = 1000;

    public $methodId = null;

    public string $senderTitle = '';

    public string $senderNumber = '';

    public $screenshot;

    public bool $showForm = false;

    protected function account(): User
    {
        // Town owner ka staff ho to bhi wallet owner ka
        return User::findOrFail(Tenant::ownerId() ?? Auth::id());
    }

    public function openForm(): void
    {
        $this->resetErrorBag();
        $this->showForm = true;
    }

    public function requestTopUp(): void
    {
        $this->validate([
            'amount' => 'required|numeric|min:' . self::MIN . '|max:' . self::MAX,
            'methodId' => 'required|exists:payment_methods,id',
            'senderTitle' => 'required|string|max:100',
            'senderNumber' => 'required|string|max:50',
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [], [
            'amount' => __('Amount'), 'methodId' => __('Payment method'), 'senderTitle' => __('Account title'),
            'senderNumber' => __('Account / mobile number'), 'screenshot' => __('Screenshot'),
        ]);

        $account = $this->account();
        $method = PaymentMethod::where('is_active', true)->find($this->methodId);
        if (! $method) {
            $this->addError('methodId', __('This payment method is no longer available.'));

            return;
        }

        if (WalletTransaction::where('user_id', $account->id)->where('type', 'deposit')->where('status', 'pending')->count() >= self::MAX_PENDING) {
            $this->addError('amount', __('You already have :n requests waiting for approval.', ['n' => self::MAX_PENDING]));

            return;
        }

        $dir = public_path('ss');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        // Naam hamesha apna; asal MIME se extension (user ka file naam kabhi nahi)
        $name = time() . '_' . bin2hex(random_bytes(6)) . '.' . $this->screenshot->extension();
        copy($this->screenshot->getRealPath(), $dir . DIRECTORY_SEPARATOR . $name);

        WalletTransaction::create([
            'user_id' => $account->id,
            'amount' => round((float) $this->amount, 2),
            'type' => 'deposit',
            'payment_method' => $method->method_name,
            'sender_account_title' => trim($this->senderTitle),
            'sender_account_number' => trim($this->senderNumber),
            'screenshot' => 'ss/' . $name,
            'status' => 'pending',
            'description' => 'Deposit via ' . $method->method_name . ' (Awaiting Admin Approval)',
        ]);

        $this->reset(['methodId', 'senderTitle', 'senderNumber', 'screenshot', 'showForm']);
        $this->amount = 1000;
        session()->flash('success', __('Top-up request sent. Your balance will increase as soon as the admin approves it.'));
    }

    public function render()
    {
        $account = $this->account();
        $driver = Messenger::isAutomatic() ? Messenger::settings()->driver : 'link';

        return view('livewire.wallet', [
            'account' => $account,
            'methods' => PaymentMethod::where('is_active', true)->get(),
            'pending' => WalletTransaction::where('user_id', $account->id)->where('type', 'deposit')->where('status', 'pending')->latest()->get(),
            'transactions' => WalletTransaction::where('user_id', $account->id)->latest('id')->paginate(15),
            'smsRate' => $driver === 'gateway' ? SmsGateway::rate() : null,
            'smsMonth' => MessageLog::where('owner_id', $account->id)->where('driver', 'gateway')->where('refunded', false)
                ->where('created_at', '>=', now()->startOfMonth())->selectRaw('COUNT(*) as n, COALESCE(SUM(cost), 0) as total')->first(),
        ])->layout('layouts.app');
    }
}
