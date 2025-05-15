<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'created_by',
    'updated_by',
    ];

    /**
     * Define the relationship with the Page model.
     */
    public function pages()
    {
        return $this->hasMany(Page::class, 'page_category_id');
    }
}
