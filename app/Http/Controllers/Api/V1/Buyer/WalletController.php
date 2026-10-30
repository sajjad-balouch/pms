<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\Transform;
use App\Models\PaymentMethod;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends ApiController
{
    public function show(Request $request)
    {
        $user = $request->user();

        return $this->ok([
            'balance' => round((float) $user->wallet_balance, 2),
            'pending_top_ups' => WalletTransaction::where('user_id', $user->id)->where('type', 'deposit')->where('status', 'pending')->count(),
            'recent' => WalletTransaction::where('user_id', $user->id)->latest()->limit(5)->get()->map(fn ($t) => Transform::transaction($t)),
        ]);
    }

    public function transactions(Request $request)
    {
        $page = WalletTransaction::where('user_id', $request->user()->id)
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->query('type')))
            ->latest()
            ->paginate($this->perPage());

        return $this->paginated($page, fn ($t) => Transform::transaction($t));
    }

    public function paymentMethods()
    {
        return $this->ok(PaymentMethod::where('is_active', true)->get()->map(fn ($m) => [
            'id' => $m->id,
            'name' => $m->method_name,
            'account_title' => $m->account_title,
            'account_number' => $m->account_number,
            'instructions' => $m->instructions,
        ]));
    }

    // multipart/form-data: amount, payment_method_id, sender_account_title, sender_account_number, screenshot (image)
    public function requestTopUp(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:100|max:500000',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'sender_account_title' => 'required|string|max:255',
            'sender_account_number' => 'required|string|max:255',
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $method = PaymentMethod::where('is_active', true)->findOrFail($data['payment_method_id']);

        $txn = WalletTransaction::create([
            'user_id' => $request->user()->id,
            'amount' => $data['amount'],
            'type' => 'deposit',
            'payment_method' => $method->method_name,
            'sender_account_title' => $data['sender_account_title'],
            'sender_account_number' => $data['sender_account_number'],
            'screenshot' => $this->storePublicImage($request->file('screenshot'), 'ss', 'topup'),
            'status' => 'pending',
            'description' => 'Deposit via ' . $method->method_name . ' (Awaiting Admin Approval)',
        ]);

        return $this->created(Transform::transaction($txn), __('Your top-up request has been submitted successfully!'));
    }
}
