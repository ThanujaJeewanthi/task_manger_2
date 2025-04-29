<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRoleDetail extends Model
{
    use HasFactory;

    protected $fillable = ['user_role_id', 'page_id', 'page_category_id', 'code', 'active', 'status'];
    // Define relationship with pages (if needed)
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
    public function pageCategory()
    {
        return $this->belongsTo(PageCategory::class);
    }
    protected static function booted()
    {
        // Fill code when creating
        static::creating(function ($userRoleDetail) {
            if ($userRoleDetail->page_id) {
                $page = Page::find($userRoleDetail->page_id);
                if ($page) {
                    $userRoleDetail->code = $page->code;
                }
            }
        });

        // Update code when page_id changes
        static::updating(function ($userRoleDetail) {
            if ($userRoleDetail->isDirty('page_id') && $userRoleDetail->page_id) {
                $page = Page::find($userRoleDetail->page_id);
                if ($page) {
                    $userRoleDetail->code = $page->code;
                }
            }
        });
    }
}
