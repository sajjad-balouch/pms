<?php

namespace App\Livewire\Tools;

use Livewire\Component;

class CvMaker extends Component
{
    // Personal Information
    public $full_name = 'Muhammad Sajjad';
    public $job_title = 'Senior Backend Engineer & Real Estate Tech Specialist';
    public $email = 'sajjad@example.com';
    public $phone = '+92 300 1234567';
    public $city = 'Rahim Yar Khan, Punjab, Pakistan';
    public $linkedin_url = 'linkedin.com/in/sajjad-dev';
    public $summary = 'Results-driven Senior Software Engineer with deep expertise in scalable backend architectures, high-performance database management, and full-stack solutions. Proven track record of developing mission-critical cadastral calculators, automated legal deed systems, and enterprise real estate applications using Laravel, Livewire, and modern web technologies.';

    // Theme Accent Color
    public $theme_color = '#0284c7'; // Options: #0284c7 (Sky), #059669 (Emerald), #d97706 (Amber), #334155 (Slate)

    // Dynamic Experience List
    public $experiences = [
        [
            'role' => 'Lead Full-Stack Engineer',
            'company' => 'AssanZameen Portal (Pakistan)',
            'duration' => '2024 - Present',
            'details' => 'Architected and deployed full-stack cadastral conversion engines, e-Stamp tax auditing modules, and automated contract workflows. Optimized complex relational database queries, reducing response latency by over 45%.'
        ],
        [
            'role' => 'Software Engineer',
            'company' => 'Enterprise Solutions Ltd',
            'duration' => '2021 - 2024',
            'details' => 'Designed and consumed secure RESTful APIs, built dynamic administrative dashboards, and integrated third-party verification gateways. Spearheaded automated reporting tools for corporate property portfolios.'
        ]
    ];

    // Dynamic Education List
    public $educations = [
        [
            'degree' => 'Bachelor of Science in Computer Science (BSCS)',
            'institute' => 'Khwaja Fareed University of Engineering & IT',
            'year' => '2017 - 2021'
        ]
    ];

    // Skills & Languages
    public $skills = 'PHP 8+, Laravel 11, Livewire 3, MySQL, Tailwind CSS, RESTful APIs, Git & GitHub, Android Studio, Relational Schema Design, System Architecture';
    public $languages = 'English (Professional Working), Urdu (Native)';

    // Dynamic Experience Actions
    public function addExperience()
    {
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
            ->layout('layouts.front-app', ['title' => 'Professional English CV Maker & ATS Resume Builder | AssanZameen']);
    }
}