<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_uuid',
        'user_id',
        'document_type',
        'document_file',
        'risk_score',
        'risk_verdict',
        'extracted_data',
        'audit_flags',
        'is_paid',
        'ip_address',
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'audit_flags' => 'array',
        'is_paid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}