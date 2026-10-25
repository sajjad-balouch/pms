<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HousingSocietyVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'authority',
        'status',
        'noc_number',
        'approval_date',
        'developer_name',
        'total_sanctioned_area',
        'sanctioned_plots_count',
        'mortgaged_plots_count',
        'lop_status',
        'risk_warning',
        'is_featured',
        'website_or_contact',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'approval_date' => 'date',
    ];
}