<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentImport extends Model
{
    protected $fillable = [
        'school_id',
        'original_filename',
        'file_path',
        'status',
        'total_rows',
        'processed_rows',
        'failed_rows',
        'errors',
    ];

    protected $casts = [
        'errors' => 'array',
    ];
}
