<?php

namespace App\Livewire\Tools;

use App\Support\Tools\ToolCatalog;
use Livewire\Component;

class ToolsIndex extends Component
{
    public function render()
    {
        return view('livewire.tools.tools-index', ['tools' => ToolCatalog::all()])
            ->layout('layouts.front-app', [
                'title' => __('Free Property Tools for Pakistan') . ' | Assan Zameen',
                'description' => __('Free calculators and document makers for buying, selling, renting and inheriting property in Pakistan.'),
            ]);
    }
}
