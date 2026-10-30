<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Disclaimer extends Component
{
    public function render()
    {
        $isUr = app()->getLocale() === 'ur';

        return view('livewire.pages.disclaimer')->layout('layouts.front-app', [
            'title' => ($isUr ? 'اعلانِ لاتعلقی' : 'Disclaimer') . ' | Assan Zameen',
            'description' => $isUr
                ? 'آسان زمین کی لسٹنگز، ٹولز، مضامین اور اشتہارات سے متعلق اہم معلومات۔'
                : 'Important information about listings, tools, articles and advertising on Assan Zameen.',
        ]);
    }
}
