<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Expertise extends Model
{
    use HasFactory;

    protected $table = 'expertise';

    protected $fillable = [
        'name',
    ];

    public function advisers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'adviser_expertise', 'expertise_id', 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
