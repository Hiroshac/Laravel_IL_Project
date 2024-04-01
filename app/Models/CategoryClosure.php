<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryClosure extends Model
{
    protected $table = 'category_closure';
    protected $guarded = [];

    public function ancestor()
    {
        return $this->belongsTo(Category::class, 'ancestor');
    }

    public function descendant()
    {
        return $this->belongsTo(Category::class, 'descendant');
    }
}