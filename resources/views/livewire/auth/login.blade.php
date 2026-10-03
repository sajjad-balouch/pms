<div class="min-h-screen relative flex items-center justify-center bg-slate-950 font-sans selection:bg-amber-500 selection:text-slate-900 overflow-hidden py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/20 rounded-full filter blur-[128px] pointer-events-none animate-pulse"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/20 rounded-full filter blur-[128px] pointer-events-none animate-pulse"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-500/10 rounded-full filter blur-[160px] pointer-events-none"></div>

    <div class="absolute inset-0 z-0">
        <img 
            src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=2000&q=80" 
            alt="Luxury Architecture" 
            class="w-full h-full object-cover object-center opacity-25 filter brightness-75 scale-105 transition-transform duration-10000 ease-linear transform hover:scale-100"
            onerror="this.src='https://placehold.co/1920x1080/0f172a/f59e0b?text=AssanZameen+Luxury+Estates'"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40 backdrop-blur-[2px]"></div>
    </div>

    <div class="relative z-10 w-full max-w-5xl bg-slate-900/60 backdrop-blur-2xl rounded-3xl border border-slate-800/80 shadow-2xl shadow-black/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[640px]">
        
        <div class="lg:col-span-5 relative hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-slate-900/90 via-slate-900/50 to-amber-950/30 border-r border-slate-800/60 overflow-hidden">
            <!-- Background Accent Graphic -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Brand Logo -->
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-300 flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform duration-300">
                        <i class="fa-solid fa-city text-slate-950 text-xl font-black"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black tracking-tight text-white block leading-none">AssanZameen</span>
                        <span class="text-[10px] font-bold tracking-widest uppercase text-amber-400">PropertyHub PMS</span>
                    </div>
                </a>
            </div>

            <!-- Central Value Proposition slider / info -->
            <div class="relative z-10 my-auto space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>100% Certified Property Portal</span>
                </div>

                <h1 class="text-3xl font-extrabold text-white tracking-tight leading-tight">
                    Manage Plots, Schemes & Clients in One Place.
                </h1>

                <p class="text-sm text-slate-400 leading-relaxed">
                    Designed specifically for Town Owners, Real Estate Agents, and Smart Buyers across Pakistan. Fast, secure, and transparent.
                </p>

                <!-- Key Metrics Badges -->
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div class="p-3.5 rounded-2xl bg-slate-800/40 border border-slate-700/50 backdrop-blur-md">
                        <div class="text-xl font-bold text-amber-400">100%</div>
                        <div class="text-[11px] font-medium text-slate-400">Verified Listings</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-800/40 border border-slate-700/50 backdrop-blur-md">
                        <div class="text-xl font-bold text-emerald-400">Instant</div>
                        <div class="text-[11px] font-medium text-slate-400">Urdu Allotment NOCs</div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer badge -->
            <div class="relative z-10 pt-6 border-t border-slate-800/60 flex items-center justify-between text-xs text-slate-400">
                <span>&copy; {{ date('Y') }} AssanZameen.com</span>
                <span class="text-slate-500">v3.2 Enterprise</span>
            </div>
        </div>

        <div class="lg:col-span-7 p-8 sm:p-12 flex flex-col justify-center bg-slate-900/40">
            
            <!-- Mobile Brand Header -->
            <div class="lg:hidden mb-8 text-center">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-bold text-lg">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">AssanZameen</span>
                </a>
            </div>

            <!-- Form Title -->
            <div class="mb-8 text-left">
                <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Welcome back</h2>
                <p class="text-sm text-slate-400 mt-2">Sign in to access your Town Management & Listing Dashboard</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 text-sm flex items-center gap-3 animate-fade-in">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Clean Livewire 3 Form Tag -->
            <form wire:submit="login" class="space-y-5">

                <!-- Email Input Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Email Address
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <input 
                            type="email" 
                            id="email"
                            wire:model.live="email" 
                            placeholder="name@company.com" 
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-950/60 border border-slate-700/80 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
                            required 
                            autofocus 
                        />
                    </div>
                    @error('email') 
                        <span class="text-rose-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span> 
                    @enderror
                </div>

                <!-- Password Input Field -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-amber-400 hover:text-amber-300 transition-colors font-medium">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </div>

                        <input 
                            type="password" 
                            id="password-input"
                            wire:model.live="password" 
                            placeholder="••••••••" 
                            class="block w-full pl-11 pr-12 py-3.5 bg-slate-950/60 border border-slate-700/80 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
                            required 
                        />

                        <!-- Native JS Password Toggle (No Alpine DOM Conflict) -->
                        <button 
                            type="button" 
                            onclick="
                                const pwdInput = document.getElementById('password-input');
                                const eyeIcon = document.getElementById('eye-icon');
                                if (pwdInput.type === 'password') {
                                    pwdInput.type = 'text';
                                    eyeIcon.classList.remove('fa-eye');
                                    eyeIcon.classList.add('fa-eye-slash', 'text-amber-400');
                                } else {
                                    pwdInput.type = 'password';
                                    eyeIcon.classList.remove('fa-eye-slash', 'text-amber-400');
                                    eyeIcon.classList.add('fa-eye');
                                }
                            "
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-300 focus:outline-none transition-colors"
                            tabindex="-1"
                        >
                            <i id="eye-icon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>

                    @error('password') 
                        <span class="text-rose-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span> 
                    @enderror
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input 
                            type="checkbox" 
                            wire:model="remember" 
                            class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-amber-500 focus:ring-amber-500/30 focus:ring-offset-slate-900 transition"
                        />
                        <span class="text-xs text-slate-400 group-hover:text-slate-300 transition-colors">
                            Remember me on this browser
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full relative group overflow-hidden rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold py-3.5 px-4 shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-300 active:scale-[0.99] disabled:opacity-70"
                >
                    <span wire:loading.remove class="flex items-center justify-center gap-2">
                        <span>Log In to Dashboard</span>
                        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        <span>Authenticating...</span>
                    </span>
                </button>
            </form>

           <!--  <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-800"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-slate-900/40 px-3 text-slate-500 font-semibold tracking-wider">Or continue with</span>
                </div>
            </div> -->

            <!-- Social Action Buttons -->
            <!-- <div class="grid grid-cols-2 gap-3">
                <button type="button" class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-300 text-xs font-semibold hover:bg-slate-800/80 hover:border-slate-700 transition-all duration-200">
                    <i class="fa-brands fa-google text-rose-500"></i>
                    <span>Google</span>
                </button>
                <button type="button" class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-300 text-xs font-semibold hover:bg-slate-800/80 hover:border-slate-700 transition-all duration-200">
                    <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                    <span>OTP Mobile</span>
                </button>
            </div> -->

            <div class="mt-8 pt-6 border-t border-slate-800/60 text-center">
                <p class="text-xs text-slate-400">
                    Don't have a property portal account? 
                    <a href="{{ route('register') }}" class="font-bold text-amber-400 hover:text-amber-300 hover:underline transition-colors ml-1">
                        Register Housing Scheme / Dealer Account
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>