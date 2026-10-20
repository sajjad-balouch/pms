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
    public $slug;
    public $category = 'Property Guides';
    public $summary;
    public $content;
    public $tags;
    public $read_time = 3;
    public $is_featured = false;
    public $status = 'published';
    public $new_image;
    public $existing_image;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $this->post_id,
            'category' => 'required|string|max:100',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'tags' => 'nullable|string|max:255',
            'read_time' => 'required|numeric|min:1',
            'status' => 'required|in:published,draft',
            'new_image' => 'nullable|image|max:3072',
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
        $this->slug = '';
        $this->category = 'Property Guides';
        $this->summary = '';
        $this->content = '';
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
        $this->validate();

        try {
            $imagePath = $this->existing_image;

            if ($this->new_image) {
                $destination = public_path('blog_images');
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0755, true);
                }
                $filename = time() . '_' . uniqid() . '.' . $this->new_image->getClientOriginalExtension();
                copy($this->new_image->getRealPath(), $destination . DIRECTORY_SEPARATOR . $filename);
                $imagePath = 'blog_images/' . $filename;
            }

            Post::updateOrCreate(
                ['id' => $this->post_id],
                [
                    'user_id' => Auth::id(),
                    'title' => $this->title,
                    'slug' => Str::slug($this->slug),
                    'category' => $this->category,
                    'featured_image' => $imagePath,
                    'summary' => $this->summary,
                    'content' => $this->content,
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
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->category = $post->category;
        $this->summary = $post->summary;
        $this->content = $post->content;
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