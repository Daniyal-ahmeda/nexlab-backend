<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestResult extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'diagnostic_test_id',
        'lab_name',
        'test_date',
        'report_date',
        'pdf_url',
    ];

    protected function casts(): array
    {
        return [
            'test_date' => 'date',
            'report_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diagnosticTest(): BelongsTo
    {
        return $this->belongsTo(DiagnosticTest::class);
    }

    public function biomarkers(): HasMany
    {
        return $this->hasMany(Biomarker::class);
    }
}
