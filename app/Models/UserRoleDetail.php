<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRoleDetail extends Model
{
    use HasFactory;

    protected $fillable = ['user_role_id', 'page_id', 'page_category_id','code','active'];

    // Define relationship with pages (if needed)
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
    public function pageCategory()
    {
        return $this->belongsTo(PageCategory::class);
    }
}
