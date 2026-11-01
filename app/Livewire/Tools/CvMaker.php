<?php

namespace App\Livewire\Tools;

use Livewire\Component;

class CvMaker extends Component
{
    // Personal Information
    public $full_name = '';
    public $job_title = '';
    public $email = '';
    public $phone = '';
    public $city = '';
    public $linkedin_url = '';
    public $summary = '';

    // Theme Accent Color
    public $theme_color = '#0284c7'; // Options: #0284c7 (Sky), #059669 (Emerald), #d97706 (Amber), #334155 (Slate)

    // Dynamic Experience List
    public $experiences = [
        ['role' => '', 'company' => '', 'duration' => '', 'details' => ''],
    ];

    // Dynamic Education List
    public $educations = [
        ['degree' => '', 'institute' => '', 'year' => ''],
    ];

    // Skills & Languages
    public $skills = '';
    public $languages = '';

    private const THEME_COLORS = ['#0284c7', '#059669', '#d97706', '#334155'];

    public function updatedThemeColor()
    {
        // Yeh value style attribute mein jati hai, sirf allowed rang
        if (! in_array($this->theme_color, self::THEME_COLORS, true)) {
            $this->theme_color = self::THEME_COLORS[0];
        }
    }

    // Dynamic Experience Actions
    public function addExperience()
    {
        if (count($this->experiences) >= 10) {
            return;
        }

        $this->experiences[] = [
            'role' => '',
            'company' => '',
            'duration' => '',
            'details' => ''
        ];
    }

    public function removeExperience($index)
    {
        unset($this->experiences[$index]);
        $this->experiences = array_values($this->experiences);
    }

    // Dynamic Education Actions
    public function addEducation()
    {
        if (count($this->educations) >= 6) {
            return;
        }

        $this->educations[] = [
            'degree' => '',
            'institute' => '',
            'year' => ''
        ];
    }

    public function removeEducation($index)
    {
        unset($this->educations[$index]);
        $this->educations = array_values($this->educations);
    }

    public function render()
    {
        return view('livewire.tools.cv-maker')
            ->layout('layouts.front-app', \App\Support\Tools\ToolCatalog::meta('cv'));
    }
}