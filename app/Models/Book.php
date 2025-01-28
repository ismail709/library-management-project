<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function collections(){
        return $this->belongsToMany(Collection::class,'book_collection');
    }
}
