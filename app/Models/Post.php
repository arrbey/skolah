<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'category',
        'author_id',
        'status',
        'view_count'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : 'https://placehold.co/800x450?text=Blog+Image';
    }
}
