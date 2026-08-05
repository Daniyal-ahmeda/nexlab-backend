<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Biomarker extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_result_id',
        'name',
        'value',
        'unit',
        'reference_range',
        'status',
    ];

    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResult::class);
    }
}
