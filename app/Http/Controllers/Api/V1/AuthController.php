<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:user,town_owner,agent', // never admin
            'password' => ['required', 'confirmed', Password::min(8)],
            'device_name' => 'nullable|string|max:100',
        ]);

        $isBuyer = $data['role'] === 'user';
        $needsApproval = ! $isBuyer && (bool) SystemSetting::getSettings()->require_provider_approval;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'trial_ends_at' => $isBuyer ? Carbon::now()->addDays(3) : null,
            'is_approved' => ! $needsApproval,
        ]);

        event(new \Illuminate\Auth\Events\Registered($user));

        if ($needsApproval) {
            return $this->created(['user' => Transform::user($user), 'token' => null, 'requires_approval' => true],
                __('Account registered successfully! Pending admin approval.'));
        }

        return $this->created([
            'user' => Transform::user($user),
            'token' => $user->createToken($data['device_name'] ?? 'android')->plainTextToken,
            'requires_approval' => false,
        ], __('Account registered successfully! You can log in now.'));
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = User::where('email', strtolower($data['email']))->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => [__('These credentials do not match our records.')]]);
        }

        if (! $user->is_approved) {
            return $this->fail(__('Your account is pending admin approval or has been blocked.'), 403);
        }

        return $this->ok([
            'user' => Transform::user($user),
            'token' => $user->createToken($data['device_name'] ?? 'android')->plainTextToken,
        ]);
    }

    public function me(Request $request)
    {
        return $this->ok(['user' => Transform::user($request->user())]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($data);

        return $this->ok(['user' => Transform::user($user->fresh())], __('Profile updated.'));
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages(['current_password' => [__('The current password is incorrect.')]]);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        // Sign out every other device
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return $this->ok(null, __('Password changed. Other devices have been signed out.'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok(null, __('Logged out.'));
    }

    public function logoutEverywhere(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->ok(null, __('Logged out from all devices.'));
    }
}
