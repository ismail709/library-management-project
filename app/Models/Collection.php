<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    /** @use HasFactory<\Database\Factories\CollectionFactory> */
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function books(){
        return $this->belongsToMany(Book::class,'book_collection');
    }
}
