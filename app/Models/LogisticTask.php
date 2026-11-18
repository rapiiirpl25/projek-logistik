<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_code',
        'title',
        'description',
        'task_date',
        'operator_id',
        'status',
        'operator_report',
        'issue_note',
        'admin_feedback',
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}