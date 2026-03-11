<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Copilot generated fillable attributes based on the users table and Microsoft OAuth fields
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'phone',
        'preferred_adviser_id',
        'microsoft_token',
        'microsoft_refresh_token',
        'microsoft_token_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'microsoft_token_expires_at' => 'datetime',
        ];
    }

    // Relationships

    /** Adviser's areas of expertise (adviser_expertise pivot). */
    public function expertise()
    {
        return $this->belongsToMany(Expertise::class, 'adviser_expertise', 'user_id', 'expertise_id');
    }

    /** Modules/areas a student needs help with (student_modules pivot). */
    public function modules()
    {
        return $this->belongsToMany(Expertise::class, 'student_modules', 'student_id', 'expertise_id');
    }

    /** The adviser a student selected at registration time. */
    public function preferredAdviser()
    {
        return $this->belongsTo(User::class, 'preferred_adviser_id');
    }

    public function studentBookings()
    {
        return $this->hasMany(Booking::class, 'student_id');
    }

    public function adviserBookings()
    {
        return $this->hasMany(Booking::class, 'adviser_id');
    }

    public function availability()
    {
        return $this->hasMany(Availability::class, 'adviser_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Helper methods
    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isAdviser()
    {
        return $this->role === 'adviser';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    //Copilot generated method to check if user has valid Microsoft token

    public function hasMicrosoftToken()
    {
        try {
            return $this->microsoft_token && $this->microsoft_token_expires_at && $this->microsoft_token_expires_at->isFuture();
        } catch (\Exception $e) {
            return false;
        }
    }
}
