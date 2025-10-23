<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'date',
        'summary',
        'content',
        'image',
        'read_time',
        'views',
    ];

    protected $casts = [
        'date' => 'date',
        'views' => 'integer',
    ];

    protected $with = ['user', 'category']; // Eager load by default

    // RELATIONSHIPS
    // cek bagian sini nih mas, kira kira dah sesuai belum
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // SCOPES
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('title', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
        }
        return $query;
    }

    public function scopePopular($query, $limit = 5)
    {
        return $query->orderBy('views', 'desc')->limit($limit);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc');
    }

    // HELPER METHODS
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getReadTimeInMinutesAttribute()
    {
        return (int) filter_var($this->read_time, FILTER_SANITIZE_NUMBER_INT);
    }
}