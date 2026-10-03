<div class="bg-slate-950 min-h-screen text-slate-100 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-12">
        
        <!-- Header -->
        <div class="text-center space-y-3 max-w-2xl mx-auto">
            <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest rounded-full">
                <i class="fa-solid fa-headset mr-1"></i> Get In Touch
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">Contact PrimeEstates</h1>
            <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                Have questions regarding land administration records, ownership verification, or housing scheme NOCs? Our team is available 24/7 to assist you.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Information Side Column -->
            <div class="space-y-4">
                <!-- Phone Card -->
                <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-3xl flex items-center gap-4 hover:border-amber-500/30 transition-all">
                    <div class="w-12 h-12 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-2xl flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-phone-volume"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">For Help</h4>
                        <a href="tel:+923078617447" class="text-sm font-extrabold text-white hover:text-amber-400 transition-colors">0307-8617447</a>
                    </div>
                </div>

                <!-- Email Card -->
                <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-3xl flex items-center gap-4 hover:border-amber-500/30 transition-all">
                    <div class="w-12 h-12 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-2xl flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Official Support Email</h4>
                        <a href="mailto:support@primeestates.com" class="text-sm font-extrabold text-white hover:text-amber-400 transition-colors">support@assanzameen.com</a>
                    </div>
                </div>

                <!-- Office Location Card -->
                <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-3xl flex items-center gap-4 hover:border-amber-500/30 transition-all">
                    <div class="w-12 h-12 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-2xl flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Headquarters</h4>
                        <p class="text-xs font-semibold text-slate-200 mt-0.5">Chachran Road Zahirpir</p>
                    </div>
                </div>

                <!-- Business Hours Box -->
                <div class="bg-slate-900/40 border border-slate-800/80 p-6 rounded-3xl space-y-2">
                    <h5 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-regular fa-clock text-amber-400"></i> Portal Operating Hours
                    </h5>
                    <div class="text-xs text-slate-400 space-y-1">
                        <div class="flex justify-between"><span>Mon - Fri:</span> <strong class="text-slate-200">9:00 AM - 6:00 PM</strong></div>
                        <div class="flex justify-between"><span>Saturday:</span> <strong class="text-slate-200">10:00 AM - 3:00 PM</strong></div>
                        <div class="flex justify-between"><span>Sunday:</span> <span class="text-rose-400 font-bold">Closed</span></div>
                    </div>
                </div>
            </div>

            <!-- Contact Form Area -->
            <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 p-8 rounded-3xl space-y-6 shadow-2xl relative">
                
                @if (session()->has('success_message'))
                    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-2xl text-xs flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-lg shrink-0"></i>
                        <div>{{ session('success_message') }}</div>
                    </div>
                @endif

                <form wire:submit.prevent="submitInquiry" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label class="text-xs text-slate-400 block mb-1 font-semibold">Full Name <span class="text-amber-400">*</span></label>
                            <input type="text" wire:model="name" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 transition-all placeholder:text-slate-600" placeholder="e.g. Ali Raza">
                            @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label class="text-xs text-slate-400 block mb-1 font-semibold">Email Address <span class="text-amber-400">*</span></label>
                            <input type="email" wire:model="email" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 transition-all placeholder:text-slate-600" placeholder="e.g. ali@example.com">
                            @error('email') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Contact Phone Field -->
                        <div>
                            <label class="text-xs text-slate-400 block mb-1 font-semibold">Contact Phone Number <span class="text-amber-400">*</span></label>
                            <input type="text" wire:model="phone" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 transition-all placeholder:text-slate-600" placeholder="e.g. +92 300 1234567">
                            @error('phone') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="text-xs text-slate-400 block mb-1 font-semibold">Subject <span class="text-amber-400">*</span></label>
                            <input type="text" wire:model="subject" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 transition-all placeholder:text-slate-600" placeholder="e.g. Plot NOC Inquiry">
                            @error('subject') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div>
                        <label class="text-xs text-slate-400 block mb-1 font-semibold">Message <span class="text-amber-400">*</span></label>
                        <textarea rows="5" wire:model="message" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 transition-all placeholder:text-slate-600 resize-none" placeholder="Explain your inquiry in detail..."></textarea>
                        @error('message') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" wire:loading.attr="disabled" class="w-full bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-slate-950 font-black text-xs py-3.5 rounded-xl transition-all flex items-center justify-center gap-2">
                        <span wire:loading.remove><i class="fa-solid fa-paper-plane mr-1"></i> Send Message</span>
                        <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Submitting Inquiry...</span>
                    </button>
                </form>

            </div>
        </div>

    </div>
</div>