<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'page_category_id',
        'active',
    ];


    public function category()
    {
        return $this->belongsTo(PageCategory::class, 'page_category_id');
    }
}
