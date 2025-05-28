<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOptionValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'job_option_id',
        'value',
        'file_path',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Relationship to Job
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Relationship to JobOption
     */
    public function jobOption()
    {
        return $this->belongsTo(JobOption::class);
    }

    /**
     * Relationship to User who created the record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to User who last updated the record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the formatted value based on option type
     */
    public function getFormattedValueAttribute()
    {
        if (!$this->jobOption) {
            return $this->value;
        }

        switch ($this->jobOption->option_type) {
            case 'checkbox':
                return $this->value ? 'Yes' : 'No';
            case 'date':
                return $this->value ? \Carbon\Carbon::parse($this->value)->format('M d, Y') : null;
            case 'number':
                return is_numeric($this->value) ? number_format($this->value, 2) : $this->value;
            case 'file':
                return $this->file_path;
            default:
                return $this->value;
        }
    }

    /**
     * Check if this option value has a file
     */
    public function hasFile()
    {
        return !empty($this->file_path);
    }
}
