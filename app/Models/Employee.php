<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    public function salaryRecords()
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    // مجموعی ایڈوانس جو ابھی تک لیا گیا
    public function totalAdvance()
    {
        return $this->salaryRecords()->where('type', 'advance')->sum('amount');
    }

    // ایڈوانس جو ابھی تنخواہ سے کٹنا باقی ہے
    public function advanceBalance()
    {
        return max(0, $this->totalAdvance() - $this->salaryRecords()->sum('advance_deduction'));
    }
}
