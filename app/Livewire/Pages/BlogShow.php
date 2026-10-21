<?php

namespace App\Livewire\Pages;

use App\Models\Post;
use App\Models\PostReview;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class BlogShow extends Component
{
    public $post;

    // Review Form States (Without Login)
    public $user_name = '';
    public $user_email = '';
    public $rating = 5;
    public $comment = '';
    public $hasLiked = false;

    protected $rules = [
        'user_name' => 'required|string|min:3|max:50',
        'user_email' => 'nullable|email|max:100',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:5|max:1000',
    ];

    public function mount($slug)
    {
        $this->post = Post::where('slug', $slug)->where('status', 'published')->firstOrFail();

        // Increment Views
        $this->post->increment('views_count');

        // Check if user already liked this post in session
        $this->hasLiked = Session::has('liked_post_' . $this->post->id);
    }

    public function toggleLike()
    {
        $sessionKey = 'liked_post_' . $this->post->id;

        if (Session::has($sessionKey)) {
            $this->post->decrement('likes_count');
            Session::forget($sessionKey);
            $this->hasLiked = false;
        } else {
            $this->post->increment('likes_count');
            Session::put($sessionKey, true);
            $this->hasLiked = true;
        }
    }

    public function submitReview()
    {
        $this->validate();

        PostReview::create([
            'post_id' => $this->post->id,
            'user_name' => strip_tags(trim($this->user_name)),
            'user_email' => $this->user_email ? trim($this->user_email) : null,
            'rating' => $this->rating,
            'comment' => strip_tags(trim($this->comment)),
            'ip_address' => request()->ip(),
            'is_approved' => true,
        ]);

        $this->user_name = '';
        $this->user_email = '';
        $this->rating = 5;
        $this->comment = '';

        session()->flash('review_success', 'Aapka review kamyabi se publish ho gaya hai!');
    }

    public function render()
    {
        $reviews = PostReview::where('post_id', $this->post->id)
            ->where('is_approved', true)
            ->latest()
            ->get();

        $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 5.0;

        $recent = Post::where('status', 'published')
            ->where('id', '!=', $this->post->id)
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.pages.blog-show', [
            'reviews' => $reviews,
            'avgRating' => $avgRating,
            'recent' => $recent,
        ])->layout('layouts.front-app');
    }
}