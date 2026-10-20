<?php

namespace App\Livewire\Pages;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    public $category = '';
    public $search = '';

    public function filterCategory($cat)
    {
        $this->category = $cat;
        $this->resetPage();
    }

    public function render()
    {
        $query = Post::where('status', 'published')->with('author');

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        return view('livewire.pages.blog-index', [
            'posts' => $query->latest()->paginate(9),
            'featured' => Post::where('status', 'published')->latest()->first(),
        ])->layout('layouts.front-app');
    }
}