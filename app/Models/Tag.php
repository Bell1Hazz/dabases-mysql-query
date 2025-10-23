<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    // RELATIONSHIPS
    // yang ini aman belum mas
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    // SCOPES
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('articles')
                     ->orderBy('articles_count', 'desc')
                     ->limit($limit);
    }
}