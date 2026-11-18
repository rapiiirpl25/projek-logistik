<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'part_name',
        'part_number',
        'quantity',
        'report_date',
        'notes',
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}