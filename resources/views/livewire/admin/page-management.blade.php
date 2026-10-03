<div class="p-6 bg-slate-900 min-h-screen text-slate-100">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-white">Dynamic Pages Management</h2>
            <p class="text-slate-400 text-xs mt-1">Manage Privacy Policy, Terms, FAQs and Custom Legal Pages.</p>
        </div>
        <button wire:click="create()" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition">
            <i class="fa-solid fa-plus"></i> Add New Page
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl mb-6 text-xs">
            {{ session('message') }}
        </div>
    @endif

    <!-- Pages Table -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900/80 text-slate-400 uppercase font-bold border-b border-slate-700">
                <tr>
                    <th class="p-4">Title</th>
                    <th class="p-4">Slug / Route</th>
                    <th class="p-4">Tag</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Last Updated</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($pages as $page)
                    <tr class="hover:bg-slate-800/40">
                        <td class="p-4 font-bold text-white">{{ $page->title }}</td>
                        <td class="p-4 text-amber-400">/page/{{ $page->slug }}</td>
                        <td class="p-4"><span class="bg-slate-700 px-2 py-1 rounded text-[10px]">{{ $page->category_tag ?? 'N/A' }}</span></td>
                        <td class="p-4">
                            @if($page->is_active)
                                <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full text-[10px]">Active</span>
                            @else
                                <span class="bg-rose-500/20 text-rose-400 border border-rose-500/30 px-2 py-0.5 rounded-full text-[10px]">Draft</span>
                            @endif
                        </td>
                        <td class="p-4 text-slate-400">{{ $page->updated_at->format('d M Y, h:i A') }}</td>
                        <td class="p-4 text-right space-x-2">
                            <button wire:click="edit({{ $page->id }})" class="bg-blue-600/20 text-blue-400 border border-blue-500/30 px-3 py-1.5 rounded-lg hover:bg-blue-600/40 transition">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $page->id }})" class="bg-rose-600/20 text-rose-400 border border-rose-500/30 px-3 py-1.5 rounded-lg hover:bg-rose-600/40 transition">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-slate-500">No pages added yet. Click 'Add New Page' to create one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Create / Edit Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md overflow-y-auto">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-3xl w-full p-6 space-y-5 shadow-2xl relative my-8">
                
                <button wire:click="closeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <h3 class="text-xl font-bold text-white">{{ $page_id ? 'Edit Page' : 'Create New Page' }}</h3>

                <form wire:submit.prevent="store" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-slate-400 block mb-1 font-bold">Page Title</label>
                            <input type="text" wire:model.live="title" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-xs text-white focus:border-amber-400 focus:outline-none">
                            @error('title') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 block mb-1 font-bold">Slug (URL)</label>
                            <input type="text" wire:model="slug" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-xs text-white focus:border-amber-400 focus:outline-none">
                            @error('slug') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-slate-400 block mb-1 font-bold">Category Tag (e.g. Legal Document)</label>
                            <input type="text" wire:model="category_tag" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-xs text-white focus:border-amber-400 focus:outline-none">
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-300">
                                <input type="checkbox" wire:model="is_active" class="rounded text-amber-500 focus:ring-amber-400">
                                <span>Publish / Active Page</span>
                            </label>
                        </div>
                    </div>

                    <!-- Alpine.js Integrated Quill.js WYSIWYG Editor -->
                    <div wire:ignore class="space-y-1">
                        <label class="text-xs text-slate-400 block font-bold">Page Content (WYSIWYG Editor)</label>
                        <div x-data="{
                            content: @entangle('content'),
                            initQuill() {
                                let quill = new Quill($refs.quillEditor, {
                                    theme: 'snow',
                                    modules: {
                                        toolbar: [
                                            [{ 'header': [1, 2, 3, false] }],
                                            ['bold', 'italic', 'underline', 'strike'],
                                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                            [{ 'color': [] }, { 'background': [] }],
                                            ['link', 'clean']
                                        ]
                                    }
                                });

                                if (this.content) {
                                    quill.clipboard.dangerouslyPasteHTML(this.content);
                                }

                                quill.on('text-change', () => {
                                    this.content = quill.root.innerHTML;
                                });
                            }
                        }" x-init="initQuill()" class="bg-slate-800 rounded-xl overflow-hidden border border-slate-700 text-white">
                            <div x-ref="quillEditor" class="min-h-[220px] text-xs"></div>
                        </div>
                    </div>
                    @error('content') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="closeModal()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl text-xs">Cancel</button>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-5 py-2 rounded-xl text-xs">Save Page</button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- External Styles & Scripts for Quill WYSIWYG Editor (No API Key Required) -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <style>
        .ql-toolbar.ql-snow {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .ql-container.ql-snow {
            border-color: #334155 !important;
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            color: #f1f5f9 !important;
            font-family: inherit;
        }
        .ql-stroke {
            stroke: #94a3b8 !important;
        }
        .ql-fill {
            fill: #94a3b8 !important;
        }
        .ql-picker {
            color: #94a3b8 !important;
        }
    </style>
</div>