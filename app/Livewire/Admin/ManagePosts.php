<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManagePosts extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';

    // Modal Form States
    public $isModalOpen = false;
    public $post_id;
    public $title;
    public $title_ur;
    public $slug;
    public $category = 'Property Guides';
    public $summary;
    public $summary_ur;
    public $content;
    public $content_ur;
    public $tags;
    public $read_time = 3;
    public $is_featured = false;
    public $status = 'published';
    public $new_image;
    public $existing_image;

    protected function rules()
    {
        return [
            // Kam az kam ek zaban poori ho; English default zaban hai
            'title' => 'nullable|required_without:title_ur|string|max:255',
            'title_ur' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $this->post_id,
            'category' => 'required|string|max:100',
            'summary' => 'required_with:title|nullable|string|max:500',
            'summary_ur' => 'required_with:title_ur|nullable|string|max:500',
            'content' => 'required_with:title|nullable|string',
            'content_ur' => 'required_with:title_ur|nullable|string',
            'tags' => 'nullable|string|max:255',
            'read_time' => 'required|numeric|min:1',
            'status' => 'required|in:published,draft',
            'new_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ];
    }

    public function updatedTitle($value)
    {
        if (!$this->post_id) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function resetForm()
    {
        $this->post_id = null;
        $this->title = '';
        $this->title_ur = '';
        $this->slug = '';
        $this->category = 'Property Guides';
        $this->summary = '';
        $this->summary_ur = '';
        $this->content = '';
        $this->content_ur = '';
        $this->tags = 'Pakistan, Real Estate, Registry, Land';
        $this->read_time = 3;
        $this->is_featured = false;
        $this->status = 'published';
        $this->new_image = null;
        $this->existing_image = null;
        $this->resetValidation();
    }

    public function save()
    {
        // Khali editor bhi '<p><br></p>' bhejta hai
        foreach (['content', 'content_ur'] as $field) {
            if (trim(strip_tags((string) $this->{$field})) === '') {
                $this->{$field} = '';
            }
        }

        $this->slug = Str::slug((string) $this->slug);
        $this->validate();

        $hasEnglish = trim((string) $this->title) !== '';

        try {
            $imagePath = $this->existing_image;

            if ($this->new_image) {
                $destination = public_path('blog_images');
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0755, true);
                }
                $filename = time() . '_' . uniqid() . '.' . $this->new_image->extension() /* real MIME type, never the user file name */;
                copy($this->new_image->getRealPath(), $destination . DIRECTORY_SEPARATOR . $filename);
                $imagePath = 'blog_images/' . $filename;
            }

            Post::updateOrCreate(
                ['id' => $this->post_id],
                [
                    'user_id' => Auth::id(),
                    // Sirf Urdu likhi ho to main fields required hain, is liye wahi text
                    'title' => $hasEnglish ? $this->title : $this->title_ur,
                    'title_ur' => $this->title_ur ?: null,
                    'slug' => Str::slug($this->slug),
                    'category' => $this->category,
                    'featured_image' => $imagePath,
                    'summary' => $hasEnglish ? $this->summary : $this->summary_ur,
                    'summary_ur' => $this->summary_ur ?: null,
                    'content' => $hasEnglish ? $this->content : $this->content_ur,
                    'content_ur' => $this->content_ur ?: null,
                    'tags' => $this->tags,
                    'read_time' => $this->read_time,
                    'is_featured' => $this->is_featured ? true : false,
                    'status' => $this->status,
                ]
            );

            session()->flash('message', $this->post_id ? 'Article کامیابی سے اپ ڈیٹ ہو گیا!' : 'نیا آرٹیکل کامیابی سے شائع ہو گیا!');
            $this->isModalOpen = false;
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'خرابی پیش آئی ہے: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $post->id;
        // Jis post ka English abhi nahi likha us ka English tab khali dikhe
        $this->title = $post->hasEnglish() ? $post->title : '';
        $this->title_ur = $post->title_ur;
        $this->slug = $post->slug;
        $this->category = $post->category;
        $this->summary = $post->hasEnglish() ? $post->summary : '';
        $this->summary_ur = $post->summary_ur;
        $this->content = $post->hasEnglish() ? $post->content : '';
        $this->content_ur = $post->content_ur;
        $this->tags = $post->tags;
        $this->read_time = $post->read_time;
        $this->is_featured = $post->is_featured;
        $this->status = $post->status;
        $this->existing_image = $post->featured_image;

        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        $post = Post::findOrFail($id);
        if ($post->featured_image && File::exists(public_path($post->featured_image))) {
            File::delete(public_path($post->featured_image));
        }
        $post->delete();
        session()->flash('message', 'Article delete kar diya gaya.');
    }

    public function render()
    {
        $query = Post::with('author');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('title_ur', 'like', '%' . $this->search . '%')
                  ->orWhere('tags', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.admin.manage-posts', [
            'posts' => $query->latest()->paginate(10),
            'totalViews' => Post::sum('views_count'),
            'totalPublished' => Post::where('status', 'published')->count(),
        ])->layout('layouts.admin-app');
    }
}