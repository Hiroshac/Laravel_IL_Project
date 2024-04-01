<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description'];

    public function ancestors()
    {
        return $this->belongsToMany(Category::class, 'category_closure', 'descendant', 'ancestor')->withPivot('depth');
    }

    public function descendants()
    {
        return $this->belongsToMany(Category::class, 'category_closure', 'ancestor', 'descendant')->withPivot('depth');
    }
}