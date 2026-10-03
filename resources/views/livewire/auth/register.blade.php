<div class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4 sm:p-6 relative overflow-hidden font-sans antialiased selection:bg-amber-500 selection:text-slate-950">
    <!-- Ambient Background Glow Effects -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-xl bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl relative z-10 my-8">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 mb-3 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-300 flex items-center justify-center text-slate-950 shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform duration-300">
                    <i class="fa-solid font-bold text-xl fa-city"></i>
                </div>
                <span class="text-2xl font-black tracking-tight text-white">
                    Assan<span class="text-amber-500">Zameen</span>
                </span>
            </a>
            <h2 class="text-2xl font-bold text-white tracking-tight">Create Your Account</h2>
            <p class="text-sm text-slate-400 mt-1">Join Pakistan's premier digital real estate ecosystem</p>
        </div>

        <!-- Session Flash Message -->
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Livewire 3 Clean Form Submission -->
        <form wire:submit="register" class="space-y-5">

            <!-- Full Name -->
            <div>
                <label for="reg-name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Full Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input 
                        type="text" 
                        id="reg-name"
                        wire:model="name" 
                        placeholder="e.g. Muhammad Ali" 
                        class="w-full pl-11 pr-4 py-3 bg-slate-900/90 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all text-sm" 
                        required 
                        autofocus 
                    />
                </div>
                @error('name') 
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p> 
                @enderror
            </div>

            <!-- Email & Phone Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Email Address -->
                <div>
                    <label for="reg-email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input 
                            type="email" 
                            id="reg-email"
                            wire:model="email" 
                            placeholder="name@example.com" 
                            class="w-full pl-11 pr-4 py-3 bg-slate-900/90 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all text-sm" 
                            required 
                        />
                    </div>
                    @error('email') 
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="reg-phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Phone Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <input 
                            type="text" 
                            id="reg-phone"
                            wire:model="phone" 
                            placeholder="0300 1234567" 
                            class="w-full pl-11 pr-4 py-3 bg-slate-900/90 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all text-sm" 
                            required 
                        />
                    </div>
                    @error('phone') 
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>
            </div>

            <!-- Role Selection Cards -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Select Account Type</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    
                    <!-- Normal User Card -->
                    <label class="relative cursor-pointer p-3.5 rounded-xl border flex flex-col items-center text-center transition-all {{ $role === 'user' ? 'border-amber-500 bg-amber-500/10 text-white shadow-lg shadow-amber-500/10' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700' }}">
                        <input type="radio" wire:model.live="role" value="user" class="sr-only">
                        <i class="fa-solid fa-user-check text-lg mb-1.5 {{ $role === 'user' ? 'text-amber-400' : 'text-slate-500' }}"></i>
                        <span class="text-xs font-bold block">Normal User</span>
                        <span class="text-[10px] text-amber-400 font-medium mt-0.5">3 Days Free Trial</span>
                    </label>

                    <!-- Town Owner Card -->
                    <label class="relative cursor-pointer p-3.5 rounded-xl border flex flex-col items-center text-center transition-all {{ $role === 'town_owner' ? 'border-amber-500 bg-amber-500/10 text-white shadow-lg shadow-amber-500/10' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700' }}">
                        <input type="radio" wire:model.live="role" value="town_owner" class="sr-only">
                        <i class="fa-solid fa-building-user text-lg mb-1.5 {{ $role === 'town_owner' ? 'text-amber-400' : 'text-slate-500' }}"></i>
                        <span class="text-xs font-bold block">Town Owner</span>
                        <span class="text-[10px] text-slate-400 mt-0.5">Society Admin</span>
                    </label>

                    <!-- Property Agent Card -->
                    <label class="relative cursor-pointer p-3.5 rounded-xl border flex flex-col items-center text-center transition-all {{ $role === 'agent' ? 'border-amber-500 bg-amber-500/10 text-white shadow-lg shadow-amber-500/10' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700' }}">
                        <input type="radio" wire:model.live="role" value="agent" class="sr-only">
                        <i class="fa-solid fa-briefcase text-lg mb-1.5 {{ $role === 'agent' ? 'text-amber-400' : 'text-slate-500' }}"></i>
                        <span class="text-xs font-bold block">Property Agent</span>
                        <span class="text-[10px] text-slate-400 mt-0.5">Dealer Portal</span>
                    </label>

                </div>
                @error('role') 
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p> 
                @enderror
            </div>

            <!-- Password Fields Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Password -->
                <div>
                    <label for="reg-password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input 
                            type="password" 
                            id="reg-password"
                            wire:model="password" 
                            placeholder="••••••••" 
                            class="w-full pl-11 pr-10 py-3 bg-slate-900/90 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all text-sm" 
                            required 
                        />
                        <button 
                            type="button" 
                            onclick="
                                const input = document.getElementById('reg-password');
                                const icon = document.getElementById('pass-icon');
                                if (input.type === 'password') {
                                    input.type = 'text';
                                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                                    icon.classList.add('text-amber-400');
                                } else {
                                    input.type = 'password';
                                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                                    icon.classList.remove('text-amber-400');
                                }
                            "
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 focus:outline-none"
                            tabindex="-1"
                        >
                            <i id="pass-icon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    @error('password') 
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="reg-password-confirm" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <input 
                            type="password" 
                            id="reg-password-confirm"
                            wire:model="password_confirmation" 
                            placeholder="••••••••" 
                            class="w-full pl-11 pr-10 py-3 bg-slate-900/90 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all text-sm" 
                            required 
                        />
                        <button 
                            type="button" 
                            onclick="
                                const input = document.getElementById('reg-password-confirm');
                                const icon = document.getElementById('confirm-pass-icon');
                                if (input.type === 'password') {
                                    input.type = 'text';
                                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                                    icon.classList.add('text-amber-400');
                                } else {
                                    input.type = 'password';
                                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                                    icon.classList.remove('text-amber-400');
                                }
                            "
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 focus:outline-none"
                            tabindex="-1"
                        >
                            <i id="confirm-pass-icon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                wire:loading.attr="disabled"
                class="w-full mt-2 py-3.5 px-6 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-bold text-sm tracking-wide shadow-lg shadow-amber-500/20 hover:shadow-amber-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 group disabled:opacity-70 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="register" class="flex items-center gap-2">
                    <span>Create Account</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </span>
                <span wire:loading wire:target="register" class="flex items-center gap-2">
                    <i class="fa-solid fa-spinner animate-spin"></i>
                    <span>Processing Registration...</span>
                </span>
            </button>
        </form>

        <!-- Footer Link -->
        <div class="mt-8 text-center border-t border-slate-800/80 pt-6">
            <p class="text-sm text-slate-400">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-amber-500 hover:text-amber-400 hover:underline transition-colors ml-1">
                    Sign in here
                </a>
            </p>
        </div>
    </div>
</div>