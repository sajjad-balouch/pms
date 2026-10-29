<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    const DISK = 'local'; // private: storage/app/private

    const TYPES = [
        'cnic_front' => 'CNIC (Front)',
        'cnic_back' => 'CNIC (Back)',
        'photo' => 'Photo',
        'booking_form' => 'Signed Booking Form',
        'other' => 'Other',
    ];

    protected $fillable = ['customer_id', 'type', 'title', 'path', 'mime', 'size', 'uploaded_by'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function label(): string
    {
        return $this->title ?: (self::TYPES[$this->type] ?? ucfirst($this->type));
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime, 'image/');
    }
}
