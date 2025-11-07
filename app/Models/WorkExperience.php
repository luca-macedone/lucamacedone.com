<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WorkExperience extends Model
{
    use HasFactory;
    use Cacheable;

    protected $fillable = [
        'job_title',
        'company',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'key_achievements',
        'technologies',
        'sort_order',
        'is_active',
        'company_logo',
        'company_url',
        'employment_type'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
        'key_achievements' => 'array',
        'technologies' => 'array',
    ];

    /**
     * Scope per esperienze attive
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per ordinamento
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('is_current', 'desc')
            ->orderBy('start_date', 'desc')
            ->orderBy('sort_order', 'asc');
    }

    /**
     * Get formatted date period
     */
    public function getFormattedPeriodAttribute()
    {
        $start = $this->start_date->format('M Y');
        $end = $this->is_current ? 'Present' : $this->end_date->format('M Y');

        return "{$start} - {$end}";
    }

    /**
     * Get duration in human readable format
     */
    public function getDurationAttribute()
    {
        $end = $this->is_current ? now() : $this->end_date;

        $diff = $this->start_date->diff($end);

        $years = $diff->y;
        $months = $diff->m;

        $duration = [];
        if ($years > 0) {
            $duration[] = $years . ' ' . ($years === 1 ? 'year' : 'years');
        }
        if ($months > 0) {
            $duration[] = $months . ' ' . ($months === 1 ? 'month' : 'months');
        }

        return implode(' ', $duration) ?: 'Less than a month';
    }

    /**
     * Get employment type label
     */
    public function getEmploymentTypeLabelAttribute()
    {
        return ucfirst(str_replace('-', ' ', $this->employment_type));
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'description' => 'nullable|string|max:2000',
            'key_achievements' => 'nullable|array',
            'key_achievements.*' => 'string|max:500',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:100',
            'company_url' => 'nullable|url',
            'employment_type' => 'in:full-time,part-time,contract,freelance,internship',
            'company_logo' => 'nullable|image|max:2048',
        ];
    }
}
