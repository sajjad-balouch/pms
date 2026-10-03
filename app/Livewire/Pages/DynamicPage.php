<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Page;

class DynamicPage extends Component
{
    public $page;

    public function mount($slug)
    {
        $this->page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.pages.dynamic-page')
            ->layout('layouts.front-app', ['title' => $this->page->title]);
    }
}