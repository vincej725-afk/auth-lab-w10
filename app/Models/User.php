<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 'role' is deliberately NOT fillable: a user must never be able
    // to promote themselves by adding role=admin to a form.
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Courses this user OWNS (teaches)
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // Courses this user is ENROLLED in (as a student)
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }

    // ---- Role helpers (used in Part B and Part C) ----
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
