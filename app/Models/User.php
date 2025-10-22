<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ===== RELATIONSHIPS =====
    
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // ===== SCOPES =====
    
    /**
     * Eager load articles count
     */
    public function scopeWithArticlesCount($query)
    {
        return $query->withCount('articles');
    }

    /**
     * Get users ordered by most articles
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('articles')
                     ->orderBy('articles_count', 'desc')
                     ->limit($limit);
    }

    /**
     * Get active users (yang punya minimal 1 artikel)
     */
    public function scopeActive($query)
    {
        return $query->has('articles');
    }

    // ===== HELPER METHODS =====
    
    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar 
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2563eb&color=fff';
    }

    /**
     * Get total articles count
     */
    public function getArticlesCountAttribute()
    {
        return $this->articles()->count();
    }

    /**
     * Get total views across all articles
     */
    public function getTotalViewsAttribute()
    {
        return $this->articles()->sum('views');
    }
}