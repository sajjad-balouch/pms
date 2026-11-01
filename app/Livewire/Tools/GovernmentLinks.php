<?php

namespace App\Livewire\Tools;

use App\Support\Tools\GovernmentServices;
use App\Support\Tools\ToolCatalog;
use Livewire\Component;

class GovernmentLinks extends Component
{
    public function render()
    {
        return view('livewire.tools.government-links', ['groups' => GovernmentServices::groups()])
            ->layout('layouts.front-app', ToolCatalog::meta('government'));
    }
}
