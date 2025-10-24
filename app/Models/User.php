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
        'role',
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
    
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeAuthors($query)
    {
        return $query->where('role', 'author');
    }

    public function scopeWithArticlesCount($query)
    {
        return $query->withCount('articles');
    }

    // ===== HELPER METHODS =====
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAuthor()
    {
        return $this->role === 'author';
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar 
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2563eb&color=fff';
    }

    public function getArticlesCountAttribute()
    {
        return $this->articles()->count();
    }

    public function getTotalViewsAttribute()
    {
        return $this->articles()->sum('views');
    }
}