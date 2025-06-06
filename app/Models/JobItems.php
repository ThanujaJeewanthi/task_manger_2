<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobItems extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'job_id',
        'item_id',
        'quantity',
        'custom_item_description',
        'notes',
        'issue_description',
        'addition_stage',
        'added_by',
        'added_at',
        'active',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
