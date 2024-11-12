<?php

namespace App\Models;

// use Attribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mcpe extends Model
{
    use HasFactory;

    protected $fillable = [
        'item',
        'deskripsi',
        'image',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/public/posts/' . $image),
        );
    }
}
