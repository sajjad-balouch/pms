<?php

namespace App\Livewire\Admin;

use App\Models\ContactInquiry;
use Livewire\Component;
use Livewire\WithPagination;

class ManageInquiries extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    
    // Explicitly define property to avoid "Undefined variable" error
    public $selectedInquiry = null; 
    public $newStatus = '';

    public function viewInquiry($id)
    {
        $inquiry = ContactInquiry::find($id);
        if ($inquiry) {
            $this->selectedInquiry = $inquiry;
            $this->newStatus = $inquiry->status;
        }
    }

    public function updateStatus($inquiryId = null, $status = null)
    {
        $id = $inquiryId ?? $this->selectedInquiry?->id;
         $newStat = $status ?? $this->newStatus;

        $inquiry = ContactInquiry::find($id);

        if ($inquiry && in_array($newStat, ['pending', 'contacted', 'resolved', 'closed'])) {
            $inquiry->update(['status' => $newStat]);
            $this->selectedInquiry = null; // Reset modal state
            session()->flash('success', "Inquiry status updated successfully.");
        }
    }

    public function closeStatusModal()
    {
        $this->selectedInquiry = null;
    }

    public function render()
    {
        $inquiries = ContactInquiry::query()
            ->when($this->statusFilter !== 'all', fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn($q) => $q->where(fn($sub) => $sub->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.manage-inquiries', [
            'inquiries' => $inquiries,
        ])->layout('layouts.admin-app');
    }
}