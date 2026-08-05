<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiagnosticTest extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'subtitle',
        'description',
        'category',
        'price',
        'reports_in_hours',
        'sample_type',
        'fasting_required',
        'is_package',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'reports_in_hours' => 'integer',
            'fasting_required' => 'boolean',
            'is_package' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function test_results(): HasMany
    {
        return $this->testResults();
    }
}
