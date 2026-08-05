<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'diagnostic_test_id',
        'partner_lab_id',
        'is_home_collection',
        'date',
        'time_slot',
        'patient_name',
        'total_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_home_collection' => 'boolean',
            'date' => 'date',
            'total_amount' => 'decimal:2',
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

    public function partnerLab(): BelongsTo
    {
        return $this->belongsTo(PartnerLab::class);
    }
}
