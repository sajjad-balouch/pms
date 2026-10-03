<x-front-app-layout>
    <div class="bg-slate-950 min-h-screen text-slate-100 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-12">
            
            <div class="text-center space-y-3">
                <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest rounded-full">
                    Get In Touch
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white">Contact PrimeEstates</h1>
                <p class="text-slate-400 text-sm">Have questions about land transfers, ownership verify or housing NOCs?</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Info Cards -->
                <div class="space-y-4">
                    <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-3xl flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-500/10 text-amber-400 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Help Desk</h4>
                            <p class="text-xs text-slate-400">+92 (042) 111-774-633</p>
                        </div>
                    </div>
                    <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-3xl flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-500/10 text-amber-400 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Official Email</h4>
                            <p class="text-xs text-slate-400">support@primeestates.com</p>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 p-8 rounded-3xl space-y-6">
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-slate-400 block mb-1">Full Name</label>
                                <input type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500" placeholder="John Doe">
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 block mb-1">Email Address</label>
                                <input type="email" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500" placeholder="john@example.com">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 block mb-1">Subject</label>
                            <input type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500" placeholder="Property Inquiry">
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 block mb-1">Message</label>
                            <textarea rows="4" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500" placeholder="Type your message here..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs py-3.5 rounded-xl transition-colors">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-front-app-layout>