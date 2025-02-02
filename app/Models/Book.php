<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory,SoftDeletes;

    protected $guarded = [];
    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function collections(){
        return $this->belongsToMany(Collection::class,'book_collection');
    }

    protected static function booted(){
        static::creating(function (Book $book) {
            $baseSlug = Str::slug($book->title); // Generate base slug
            $slug = $baseSlug;
            $count = 1;

            // Ensure uniqueness
            while (Book::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }

            $book->slug = $slug;
        });

        static::updating(function (Book $book) {
            $baseSlug = Str::slug($book->title); // Generate base slug
            $slug = $baseSlug;
            $count = 1;

            // Ensure uniqueness
            while (Book::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }

            $book->slug = $slug;
        });
    }
}
