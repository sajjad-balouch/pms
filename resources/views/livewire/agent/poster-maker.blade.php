<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="posterStudio()" x-init="initStudio()">

    <!-- Google Web Fonts Preload -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Header Section -->
    <div class="bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-palette text-amber-400"></i>
                <span>Premium Poster Studio (HD Graphic Studio)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Design high-impact marketing posters with modern typography and sleek themes</p>
        </div>

        <button type="button" @click="downloadPoster()" 
                class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-slate-950 font-black text-xs rounded-xl transition flex items-center gap-2 shadow-lg shadow-amber-500/25 cursor-pointer">
            <i class="fa-solid fa-cloud-arrow-down text-sm"></i>
            <span>Download Poster (Full HD)</span>
        </button>
    </div>

    <!-- Main Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Side Controls Panel (5 Cols) -->
        <div class="lg:col-span-5 bg-slate-800/90 p-6 rounded-2xl border border-slate-700/80 shadow-md space-y-4">
            
            <div class="border-b border-slate-700/80 pb-3 flex justify-between items-center">
                <span class="text-xs font-bold text-white uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-sliders text-indigo-400"></i>
                    <span>Poster Editor & Controls</span>
                </span>
                <span class="text-[10px] font-bold bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">Studio Mode</span>
            </div>

            <!-- Property Switcher -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Select Property</label>
                <select wire:change="selectProperty($event.target.value)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-indigo-500 font-medium">
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" {{ $selectedPropertyId ==$prop->id ? 'selected' : '' }}>
                            {{ $prop->title }} ({{ $prop->area_size }}) - Rs. {{ number_format($prop->price) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Format Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Format & Size</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="setFormat('status')"
                            :class="format === 'status' ? 'bg-indigo-600 text-white font-black ring-2 ring-indigo-400/30' : 'bg-slate-900 text-slate-400 border border-slate-700'"
                            class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-mobile-screen"></i>
                        <span>WhatsApp Status (9:16)</span>
                    </button>
                    <button type="button" @click="setFormat('post')"
                            :class="format === 'post' ? 'bg-indigo-600 text-white font-black ring-2 ring-indigo-400/30' : 'bg-slate-900 text-slate-400 border border-slate-700'"
                            class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-square"></i>
                        <span>Facebook Post (Square 1:1)</span>
                    </button>
                </div>
            </div>

            <!-- Main Tag & Title Inputs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Highlight Badge</label>
                    <input type="text" x-model="customTag" @input="draw()" 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-amber-400 font-bold focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Demand Price (PKR)</label>
                    <input type="text" x-model="price" @input="draw()" 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-emerald-400 font-bold font-mono focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Poster Title</label>
                <input type="text" x-model="title" @input="draw()" 
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-bold focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Location & Sector</label>
                <input type="text" x-model="location" @input="draw()" 
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Extra Bullet Highlights -->
            <div class="space-y-2 pt-2 border-t border-slate-700/80">
                <label class="block text-[11px] font-bold text-amber-400 uppercase">Features & Highlights</label>
                <input type="text" x-model="feature1" @input="draw()" placeholder="Feature 1" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-amber-500">
                <input type="text" x-model="feature2" @input="draw()" placeholder="Feature 2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-amber-500">
                <input type="text" x-model="feature3" @input="draw()" placeholder="Feature 3" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>

            <!-- Color Theme Selector -->
            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Color Theme</label>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="setTheme('gold')"
                            :class="theme === 'gold' ? 'border-amber-400 ring-2 ring-amber-400/20' : 'border-slate-700'"
                            class="p-2 rounded-xl border bg-slate-950 text-xs font-bold text-amber-400 flex items-center justify-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Gold
                    </button>
                    <button type="button" @click="setTheme('emerald')"
                            :class="theme === 'emerald' ? 'border-emerald-400 ring-2 ring-emerald-400/20' : 'border-slate-700'"
                            class="p-2 rounded-xl border bg-slate-950 text-xs font-bold text-emerald-400 flex items-center justify-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Emerald
                    </button>
                    <button type="button" @click="setTheme('cyan')"
                            :class="theme === 'cyan' ? 'border-cyan-400 ring-2 ring-cyan-400/20' : 'border-slate-700'"
                            class="p-2 rounded-xl border bg-slate-950 text-xs font-bold text-cyan-400 flex items-center justify-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span> Cyan
                    </button>
                </div>
            </div>

        </div>

        <!-- Right Side Live Canvas (7 Cols) -->
        <div class="lg:col-span-7 bg-slate-900/90 p-6 rounded-3xl border border-slate-800 shadow-2xl flex flex-col items-center justify-center min-h-[580px]">
            <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>HD Live Canvas Preview</span>
            </div>

            <!-- Canvas Container -->
            <div class="rounded-2xl overflow-hidden shadow-2xl border border-slate-800 bg-black flex items-center justify-center">
                <canvas id="posterCanvas" class="max-h-[560px] w-auto object-contain"></canvas>
            </div>

            <p class="text-[11px] text-slate-500 mt-3 text-center">
                Posters are rendered in crisp 1080p Full HD resolution ready for marketing.
            </p>
        </div>

    </div>

    <!-- Live Canvas Rendering Script -->
    <script>
        function posterStudio() {
            return {
                format: 'status', // 'status' (1080x1920) or 'post' (1080x1080)
                theme: 'gold',
                title: @js($title),
                price: @js($price),
                areaSize: @js($areaSize),
                location: @js($location),
                propertyType: @js($propertyType),
                imageUrl: @js($imageUrl),
                customTag: @js($customTag),
                feature1: @js($feature1),
                feature2: @js($feature2),
                feature3: @js($feature3),
                agencyName: @js($agencyName),
                agentName: @js($agentName),
                agentPhone: @js($agentPhone),

                canvas: null,
                ctx: null,
                imgElement: null,

                initStudio() {
                    this.canvas = document.getElementById('posterCanvas');
                    this.ctx = this.canvas.getContext('2d');

                    // Make sure web fonts are loaded before drawing
                    document.fonts.ready.then(() => {
                        this.draw();
                    });

                    // Listen to Livewire dropdown changes
                    window.addEventListener('property-updated', (event) => {
                        const d = event.detail.data;
                        this.title = d.title;
                        this.price = d.price;
                        this.areaSize = d.areaSize;
                        this.location = d.location;
                        this.propertyType = d.propertyType;
                        this.imageUrl = d.imageUrl;
                        this.loadImageAndRender();
                    });

                    this.loadImageAndRender();
                },

                setFormat(fmt) {
                    this.format = fmt;
                    this.draw();
                },

                setTheme(thm) {
                    this.theme = thm;
                    this.draw();
                },

                loadImageAndRender() {
                    if (this.imageUrl) {
                        let img = new Image();
                        img.crossOrigin = 'anonymous';
                        img.onload = () => {
                            this.imgElement = img;
                            this.draw();
                        };
                        img.onerror = () => {
                            this.imgElement = null;
                            this.draw();
                        };
                        img.src = this.imageUrl;
                    } else {
                        this.imgElement = null;
                        this.draw();
                    }
                },

                draw() {
                    if (!this.canvas) return;

                    let width = 1080;
                    let isStatus = (this.format === 'status');
                    let height = isStatus ? 1920 : 1080;

                    this.canvas.width = width;
                    this.canvas.height = height;
                    let ctx = this.ctx;

                    // Standard typography definitions
                    const fontDisplay = "'Inter', system-ui, -apple-system, sans-serif";
                    const fontBody = "'Inter', system-ui, -apple-system, sans-serif";

                    // Theme Colors
                    let accent = '#fbbf24'; // Gold
                    if (this.theme === 'emerald') accent = '#10b981';
                    if (this.theme === 'cyan') accent = '#38bdf8';

                    // 1. Solid Dark Background
                    ctx.fillStyle = '#080c14';
                    ctx.fillRect(0, 0, width, height);

                    // 2. Photo Area
                    let imgHeight = isStatus ? 950 : 380;

                    if (this.imgElement) {
                        ctx.save();
                        ctx.beginPath();
                        ctx.rect(0, 0, width, imgHeight);
                        ctx.clip();
                        
                        let scale = Math.max(width / this.imgElement.width, imgHeight / this.imgElement.height);
                        let x = (width / 2) - (this.imgElement.width / 2) * scale;
                        let y = (imgHeight / 2) - (this.imgElement.height / 2) * scale;
                        ctx.drawImage(this.imgElement, x, y, this.imgElement.width * scale, this.imgElement.height * scale);
                        ctx.restore();

                        // Deep Bottom Fade Gradient
                        let fade = ctx.createLinearGradient(0, imgHeight - 180, 0, imgHeight + 2);
                        fade.addColorStop(0, 'rgba(8, 12, 20, 0)');
                        fade.addColorStop(0.75, 'rgba(8, 12, 20, 0.9)');
                        fade.addColorStop(1, '#080c14');
                        ctx.fillStyle = fade;
                        ctx.fillRect(0, imgHeight - 180, width, 185);
                    } else {
                        let placeholderGradient = ctx.createLinearGradient(0, 0, width, imgHeight);
                        placeholderGradient.addColorStop(0, '#1e1b4b');
                        placeholderGradient.addColorStop(1, '#080c14');
                        ctx.fillStyle = placeholderGradient;
                        ctx.fillRect(0, 0, width, imgHeight);
                    }

                    // 3. Top Header Bar (Agency Identity)
                    let headerH = isStatus ? 95 : 75;
                    ctx.fillStyle = 'rgba(8, 12, 20, 0.85)';
                    ctx.fillRect(35, 30, width - 70, headerH);
                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(35, 30, width - 70, headerH);

                    ctx.fillStyle = '#ffffff';
                    ctx.font = `bold ${isStatus ? '30px' : '24px'} ${fontDisplay}`;
                    ctx.textAlign = 'left';
                    ctx.fillText(this.agencyName, 60, isStatus ? 88 : 75);

                    ctx.fillStyle = accent;
                    ctx.font = `bold ${isStatus ? '24px' : '20px'} ${fontBody}`;
                    ctx.textAlign = 'right';
                    ctx.fillText('AssanZameen.com', width - 60, isStatus ? 88 : 75);

                    // 4. Hot Tag Badge
                    if (this.customTag) {
                        let tagY = imgHeight - (isStatus ? 70 : 50);
                        let tagH = isStatus ? 54 : 44;
                        ctx.fillStyle = accent;
                        ctx.fillRect(35, tagY, isStatus ? 320 : 280, tagH);
                        
                        ctx.fillStyle = '#000000';
                        ctx.font = `900 ${isStatus ? '20px' : '16px'} ${fontDisplay}`;
                        ctx.textAlign = 'center';
                        ctx.fillText(this.customTag.toUpperCase(), isStatus ? 195 : 175, tagY + (isStatus ? 35 : 28));
                    }

                    // 5. Property Title & Location
                    let startY = imgHeight + (isStatus ? 50 : 35);

                    ctx.fillStyle = '#ffffff';
                    ctx.font = `800 ${isStatus ? '44px' : '32px'} ${fontDisplay}`;
                    ctx.textAlign = 'left';
                    ctx.fillText(this.title, 35, startY);

                    ctx.fillStyle = '#94a3b8';
                    ctx.font = `600 ${isStatus ? '24px' : '18px'} ${fontBody}`;
                    ctx.fillText('📍 ' + this.location, 35, startY + (isStatus ? 48 : 34));

                    // 6. Specs Cards (Area & Property Type)
                    let specY = startY + (isStatus ? 85 : 55);
                    let boxH = isStatus ? 85 : 68;
                    let boxW = isStatus ? 300 : 260;

                    // Box 1: Area
                    ctx.fillStyle = '#111827';
                    ctx.fillRect(35, specY, boxW, boxH);
                    ctx.strokeStyle = '#374151';
                    ctx.lineWidth = 1.5;
                    ctx.strokeRect(35, specY, boxW, boxH);

                    ctx.fillStyle = '#9ca3af';
                    ctx.font = `bold ${isStatus ? '15px' : '12px'} ${fontBody}`;
                    ctx.fillText('AREA SIZE', 50, specY + (isStatus ? 30 : 24));
                    ctx.fillStyle = '#ffffff';
                    ctx.font = `800 ${isStatus ? '30px' : '24px'} ${fontDisplay}`;
                    ctx.fillText(this.areaSize, 50, specY + (isStatus ? 66 : 52));

                    // Box 2: Type
                    ctx.fillStyle = '#111827';
                    ctx.fillRect(35 + boxW + 20, specY, boxW, boxH);
                    ctx.strokeRect(35 + boxW + 20, specY, boxW, boxH);

                    ctx.fillStyle = '#9ca3af';
                    ctx.font = `bold ${isStatus ? '15px' : '12px'} ${fontBody}`;
                    ctx.fillText('PROPERTY TYPE', 35 + boxW + 35, specY + (isStatus ? 30 : 24));
                    ctx.fillStyle = '#ffffff';
                    ctx.font = `800 ${isStatus ? '30px' : '24px'} ${fontDisplay}`;
                    ctx.fillText(this.propertyType, 35 + boxW + 35, specY + (isStatus ? 66 : 52));

                    // 7. Demand Price Box
                    let priceY = specY + boxH + (isStatus ? 30 : 16);
                    let priceH = isStatus ? 110 : 84;

                    ctx.fillStyle = '#064e3b';
                    ctx.fillRect(35, priceY, width - 70, priceH);
                    ctx.strokeStyle = '#10b981';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(35, priceY, width - 70, priceH);

                    ctx.fillStyle = '#a7f3d0';
                    ctx.font = `bold ${isStatus ? '18px' : '14px'} ${fontBody}`;
                    ctx.fillText('DEMAND PRICE', 55, priceY + (isStatus ? 36 : 28));

                    ctx.fillStyle = '#ffffff';
                    ctx.font = `900 ${isStatus ? '52px' : '40px'} ${fontDisplay}`;
                    ctx.fillText('PKR ' + this.price, 55, priceY + (isStatus ? 86 : 66));

                    // 8. Highlights
                    if (isStatus) {
                        let noteY = priceY + priceH + 45;
                        ctx.fillStyle = '#f8fafc';
                        ctx.font = `bold 24px ${fontDisplay}`;
                        ctx.fillText('Features & Highlights:', 35, noteY);

                        ctx.fillStyle = '#cbd5e1';
                        ctx.font = `500 22px ${fontBody}`;
                        if (this.feature1) ctx.fillText('✔  ' + this.feature1, 45, noteY + 48);
                        if (this.feature2) ctx.fillText('✔  ' + this.feature2, 45, noteY + 92);
                        if (this.feature3) ctx.fillText('✔  ' + this.feature3, 45, noteY + 136);
                    } else {
                        let badgeY = priceY + priceH + 18;
                        let badgeH = 50;
                        let badgeW = (width - 70 - 24) / 3;

                        let feats = [this.feature1, this.feature2, this.feature3];
                        feats.forEach((feat, i) => {
                            if (!feat) return;
                            let bX = 35 + (i * (badgeW + 12));
                            
                            ctx.fillStyle = '#1e293b';
                            ctx.fillRect(bX, badgeY, badgeW, badgeH);
                            ctx.strokeStyle = '#475569';
                            ctx.lineWidth = 1;
                            ctx.strokeRect(bX, badgeY, badgeW, badgeH);

                            ctx.fillStyle = '#e2e8f0';
                            ctx.font = `600 14px ${fontBody}`;
                            ctx.textAlign = 'center';
                            ctx.fillText('✔ ' + feat, bX + (badgeW / 2), badgeY + 30);
                        });
                        ctx.textAlign = 'left';
                    }

                    // 9. Agent Contact Footer
                    let footerHeight = isStatus ? 140 : 110;
                    let footerY = height - footerHeight - 20;

                    ctx.fillStyle = '#0f172a';
                    ctx.fillRect(35, footerY, width - 70, footerHeight);
                    ctx.strokeStyle = accent;
                    ctx.lineWidth = 2.5;
                    ctx.strokeRect(35, footerY, width - 70, footerHeight);

                    ctx.fillStyle = '#ffffff';
                    ctx.font = `bold ${isStatus ? '30px' : '24px'} ${fontDisplay}`;
                    ctx.textAlign = 'left';
                    ctx.fillText(this.agentName, 60, footerY + (isStatus ? 56 : 44));

                    ctx.fillStyle = '#94a3b8';
                    ctx.font = `500 ${isStatus ? '18px' : '14px'} ${fontBody}`;
                    ctx.fillText('Contact for Details & Booking', 60, footerY + (isStatus ? 96 : 78));

                    // Phone Number Right Aligned
                    ctx.fillStyle = accent;
                    ctx.font = `900 ${isStatus ? '38px' : '32px'} ${fontDisplay}`;
                    ctx.textAlign = 'right';
                    ctx.fillText('📞 ' + this.agentPhone, width - 60, footerY + (isStatus ? 80 : 66));
                },

                downloadPoster() {
                    let link = document.createElement('a');
                    link.download = 'AssanZameen_Poster_' + Date.now() + '.png';
                    link.href = this.canvas.toDataURL('image/png', 1.0);
                    link.click();
                }
            }
        }
    </script>
</div>