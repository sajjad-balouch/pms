<div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Quill WYSIWYG CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        /* Dark Theme Styling for Quill */
        .ql-toolbar.ql-snow {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .ql-container.ql-snow {
            background-color: #020617 !important;
            border-color: #334155 !important;
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            color: #f1f5f9 !important;
            font-size: 0.875rem;
            min-height: 220px;
        }
        .ql-snow .ql-stroke {
            stroke: #94a3b8 !important;
        }
        .ql-snow .ql-fill {
            fill: #94a3b8 !important;
        }
        .ql-snow .ql-picker {
            color: #94a3b8 !important;
        }
        .ql-snow .ql-picker-options {
            background-color: #0f172a !important;
            border-color: #334155 !important;
        }
        .ql-editor.ql-blank::before {
            color: #64748b !important;
            font-style: normal;
        }
    </style>

    @if (session()->has('message'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl shadow-sm">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="text-xs font-bold">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-slate-800/90 p-6 rounded-2xl shadow-md border border-slate-700/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-newspaper text-amber-400"></i>
                <span>VIP Blog & Content Management</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">SEO articles, market guides aur AdSense traffic ke liye articles manage karein</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('blog.index') }}" target="_blank" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-750 text-indigo-400 border border-indigo-500/30 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Public Blog Dekhein</span>
            </a>

            <button wire:click="create" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-slate-950 font-black text-xs rounded-xl transition flex items-center gap-2 shadow-lg shadow-amber-500/20 cursor-pointer">
                <i class="fa-solid fa-pen-nib"></i>
                <span>Naya Article Likhein</span>
            </button>
        </div>
    </div>

    <!-- Quick Counter Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 bg-slate-900/90 rounded-2xl border border-slate-800 shadow-md flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase">Live Published Articles</span>
                <div class="text-2xl font-black text-white mt-0.5">{{ $totalPublished }}</div>
            </div>
            <i class="fa-solid fa-file-circle-check text-2xl text-emerald-400"></i>
        </div>

        <div class="p-4 bg-slate-900/90 rounded-2xl border border-slate-800 shadow-md flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase">Total Blog Readers (Views)</span>
                <div class="text-2xl font-black text-amber-400 font-mono mt-0.5">{{ number_format($totalViews) }}</div>
            </div>
            <i class="fa-solid fa-eye text-2xl text-amber-400"></i>
        </div>

        <div class="p-4 bg-slate-900/90 rounded-2xl border border-slate-800 shadow-md flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase">SEO & AdSense Status</span>
                <div class="text-xs font-bold text-emerald-400 mt-1">High CPC Enabled ✓</div>
            </div>
            <i class="fa-solid fa-chart-line text-2xl text-indigo-400"></i>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-800/90 p-4 rounded-2xl border border-slate-700/80 shadow-md">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Article title ya tag search karein..." 
               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
        
        <select wire:model.live="categoryFilter" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
            <option value="">Tamam Categories</option>
            <option value="Property Guides">Property Guides (رہنمائی)</option>
            <option value="Taxes & Laws">Taxes & Laws (ٹیکس و قوانین)</option>
            <option value="Market News">Market News (تازہ ترین خبریں)</option>
            <option value="Investment Tips">Investment Tips (سرمایہ کاری)</option>
        </select>

        <select wire:model.live="statusFilter" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
            <option value="">Tamam Statuses</option>
            <option value="published">Published (Live)</option>
            <option value="draft">Draft (Private)</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-slate-800/90 rounded-2xl shadow-md border border-slate-700/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/80">
                        <th class="px-5 py-4">Article</th>
                        <th class="px-5 py-4">Category</th>
                        <th class="px-5 py-4">Read Time</th>
                        <th class="px-5 py-4">Views</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-sm">
                    @forelse($posts as $post)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-950 border border-slate-700 shrink-0">
                                        @if($post->featured_image)
                                            <img src="{{ asset('public/' . $post->featured_image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-600"><i class="fa-solid fa-image"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-white line-clamp-1">{{ $post->title }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $post->created_at->format('d M, Y') }} • By {{ $post->author->name ?? 'Admin' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 bg-slate-900 border border-slate-700 text-amber-400 rounded-lg text-xs font-bold">
                                    {{ $post->category }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-300 font-mono text-xs">
                                {{ $post->read_time }} min read
                            </td>
                            <td class="px-5 py-4 font-mono font-bold text-emerald-400">
                                {{ number_format($post->views_count) }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full capitalize {{ $post->status === 'published' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-700 text-slate-300' }}">
                                    {{ $post->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right space-x-2">
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-xs font-semibold text-slate-300 hover:text-white">View</a>
                                <button wire:click="edit({{ $post->id }})" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 cursor-pointer">Edit</button>
                                <button wire:click="delete({{ $post->id }})" onclick="confirm('Article delete karein?') || event.stopImmediatePropagation()" class="text-xs font-semibold text-rose-400 hover:text-rose-300 cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">Abhi tak koi blog post create nahi ki gayi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-700/80">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- Create / Edit Article Modal (With WYSIWYG) -->
    <!-- Create / Edit Article Modal (With Visible Error Handling & WYSIWYG) -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md">
        <div class="bg-slate-900 rounded-3xl shadow-2xl w-full max-w-4xl border border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-nib text-amber-400"></i>
                    <span>{{ $post_id ? 'Article Edit Karein' : 'Naya VIP Article Likhein' }}</span>
                </h3>
                <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <!-- Global Error Banner (System / Server Error) -->
            @if (session()->has('error'))
                <div class="mx-6 mt-4 p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl flex items-start gap-3 text-rose-400 text-xs">
                    <i class="fa-solid fa-triangle-exclamation text-base mt-0.5 shrink-0"></i>
                    <div>
                        <span class="font-bold block">سسٹم کی خرابی (Server Error):</span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Validation Errors Summary Alert -->
            @if ($errors->any())
                <div class="mx-6 mt-4 p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-xs space-y-1.5">
                    <div class="font-bold text-rose-400 flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span>برائے مہربانی درج ذیل غلطیوں کو درست کریں ({{ count($errors) }} غلطیاں موجود ہیں):</span>
                    </div>
                    <ul class="list-disc list-inside text-rose-300/90 space-y-0.5 pr-2">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit="save" class="p-6 space-y-4 max-h-[82vh] overflow-y-auto">
                <!-- Title -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Article Title *</label>
                    <input type="text" wire:model.live.debounce.300ms="title" placeholder="Maslan: Punjab Mein Plot Registry Ka Naya Sarkari Fee Schedule" 
                           class="w-full bg-slate-950 border {{ $errors->has('title') ? 'border-rose-500 ring-1 ring-rose-500/30' : 'border-slate-800' }} rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none focus:border-amber-500">
                    @error('title') 
                        <span class="text-rose-400 text-[11px] font-semibold mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                        </span> 
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Slug -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">SEO URL Slug *</label>
                        <input type="text" wire:model="slug" placeholder="punjab-plot-registry-fee-schedule" 
                               class="w-full bg-slate-950 border {{ $errors->has('slug') ? 'border-rose-500 ring-1 ring-rose-500/30' : 'border-slate-800' }} rounded-xl px-3.5 py-2 text-xs text-amber-400 font-mono focus:outline-none focus:border-amber-500">
                        @error('slug') 
                            <span class="text-rose-400 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </span> 
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Category *</label>
                        <select wire:model="category" class="w-full bg-slate-950 border {{ $errors->has('category') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="Property Guides">Property Guides (رہنمائی)</option>
                            <option value="Taxes & Laws">Taxes & Laws (ٹیکس و قوانین)</option>
                            <option value="Market News">Market News (تازہ ترین خبریں)</option>
                            <option value="Investment Tips">Investment Tips (سرمایہ کاری)</option>
                        </select>
                        @error('category') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Read Time -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Read Time (Mins) *</label>
                        <input type="number" wire:model="read_time" class="w-full bg-slate-950 border {{ $errors->has('read_time') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-amber-500">
                        @error('read_time') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Featured Image -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Featured Cover Image (JPG, PNG, WebP - Max 3MB)</label>
                    <div class="flex items-center gap-4">
                        @if($existing_image)
                            <img src="{{ asset('public/' . $existing_image) }}" class="w-14 h-14 rounded-xl object-cover border border-slate-700 bg-slate-950 shrink-0">
                        @endif
                        <input type="file" wire:model="new_image" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20">
                    </div>
                    <div wire:loading wire:target="new_image" class="text-xs text-amber-400 mt-1">تصویر اپلوڈ ہو رہی ہے، براہِ کرم انتظار کریں...</div>
                    @error('new_image') 
                        <span class="text-rose-400 text-[11px] font-semibold mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                        </span> 
                    @enderror
                </div>

                <!-- Summary -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Article Summary (Short Snippet) *</label>
                    <textarea wire:model="summary" rows="2" placeholder="2 se 3 lines mein article ka khulasa likhein jo Google aur cards par show hoga..." 
                              class="w-full bg-slate-950 border {{ $errors->has('summary') ? 'border-rose-500 ring-1 ring-rose-500/30' : 'border-slate-800' }} rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-amber-500"></textarea>
                    @error('summary') 
                        <span class="text-rose-400 text-[11px] font-semibold mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                        </span> 
                    @enderror
                </div>

                <!-- WYSIWYG Content Editor -->
                <div wire:ignore>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Full Article Content (Rich Text Editor) *</label>
                    
                    <div x-data="{
                        content: @entangle('content'),
                        quill: null,
                        init() {
                            this.quill = new Quill($refs.editor, {
                                theme: 'snow',
                                placeholder: 'Yahan apna mukammal article headings, bold text, bullet points aur links ke sath likhein...',
                                modules: {
                                    toolbar: [
                                        [{ 'header': [1, 2, 3, false] }],
                                        ['bold', 'italic', 'underline', 'strike'],
                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                        ['blockquote', 'code-block'],
                                        [{ 'color': [] }, { 'background': [] }],
                                        ['link', 'clean']
                                    ]
                                }
                            });

                            if (this.content) {
                                this.quill.root.innerHTML = this.content;
                            }

                            this.quill.on('text-change', () => {
                                this.content = this.quill.root.innerHTML;
                            });

                            this.$watch('content', value => {
                                if (value !== this.quill.root.innerHTML) {
                                    this.quill.root.innerHTML = value || '';
                                }
                            });
                        }
                    }">
                        <div x-ref="editor"></div>
                    </div>
                </div>
                @error('content') 
                    <span class="text-rose-400 text-[11px] font-semibold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> آرٹیکل کا تفصیلی مواد لکھنا لازمی ہے ({{ $message }})
                    </span> 
                @enderror

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Tags (Comma Separated)</label>
                        <input type="text" wire:model="tags" placeholder="Plot, Registry, FBR, Filer, Punjab" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Publish Status</label>
                        <select wire:model="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="published">Published (Live to public)</option>
                            <option value="draft">Draft (Saved private)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold cursor-pointer">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-6 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-xs shadow-md cursor-pointer disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">Article Save Karein</span>
                        <span wire:loading wire:target="save">محفوظ ہو رہا ہے...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Quill Editor JS Bundle -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

</div>