<?php

namespace App\Livewire\Pages;

use App\Models\ContactInquiry;
use Livewire\Component;

class ContactUs extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $subject = '';
    public $message = '';

    protected $rules = [
        'name'    => 'required|string|min:3|max:100',
        'email'   => 'required|email|max:150',
        'phone'   => 'required|string|min:10|max:20',
        'subject' => 'required|string|min:3|max:200',
        'message' => 'required|string|min:10|max:2000',
    ];

    public function submitInquiry()
    {
        $validatedData = $this->validate();

        ContactInquiry::create($validatedData);

        // Properties reset karne ke sath validation errors bhi reset karein
        $this->reset(); 
        $this->resetValidation();

        session()->flash('success_message', 'Thank you! Your inquiry has been submitted successfully. Our representative will contact you shortly.');
    }

    public function render()
    {
        return view('livewire.pages.contact-us')->layout('layouts.front-app');
    }
}