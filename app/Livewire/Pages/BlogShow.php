<?php

namespace App\Livewire\Pages;

use App\Models\Post;
use Livewire\Component;

class BlogShow extends Component
{
    public $post;

    public function mount($slug)
    {
        $this->post = Post::where('slug', $slug)->where('status', 'published')->firstOrFail();
        // dd($this->post);
        // Increment read counter
        $this->post->increment('views_count');
    }

    public function render()
    {
        $recent = Post::where('status', 'published')
            ->where('id', '!=', $this->post->id)
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.pages.blog-show', [
            'recent' => $recent
        ])->layout('layouts.front-app');
    }
}