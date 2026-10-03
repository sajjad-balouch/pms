<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Page;
use Illuminate\Support\Str;

class PageManagement extends Component
{
    public $pages;
    public $page_id, $title, $slug, $category_tag, $content;
    public $is_active = true;
    public $isModalOpen = false;

    protected $rules = [
        'title'        => 'required|string|max:255',
        'slug'         => 'required|string|max:255',
        'category_tag' => 'nullable|string|max:100',
        'content'      => 'required|string',
        'is_active'    => 'boolean',
    ];

    public function render()
    {
        $this->pages = Page::latest()->get();
        return view('livewire.admin.page-management')->layout('layouts.admin-app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
        // Dispatch event to re-initialize WYSIWYG Editor in JS
        $this->dispatch('init-wysiwyg');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->page_id = null;
        $this->title = '';
        $this->slug = '';
        $this->category_tag = '';
        $this->content = '';
        $this->is_active = true;
    }

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function store()
    {
        $this->validate();

        Page::updateOrCreate(['id' => $this->page_id], [
            'title'        => $this->title,
            'slug'         => Str::slug($this->slug),
            'category_tag' => $this->category_tag,
            'content'      => $this->content,
            'is_active'    => $this->is_active,
        ]);

        session()->flash('message', $this->page_id ? 'Page updated successfully.' : 'Page created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $this->page_id = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->category_tag = $page->category_tag;
        $this->content = $page->content;
        $this->is_active = $page->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        Page::findOrFail($id)->delete();
        session()->flash('message', 'Page deleted successfully.');
    }
}