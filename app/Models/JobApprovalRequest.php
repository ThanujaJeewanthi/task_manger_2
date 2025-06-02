<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApprovalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'requested_by',
        'approval_user_id',
        'status',
        'request_notes',
        'approval_notes',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the job that owns the approval request
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the user who requested the approval
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who should approve the request
     */
    public function approvalUser()
    {
        return $this->belongsTo(User::class, 'approval_user_id');
    }

    /**
     * Get the user who approved/rejected the request
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to get pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
