<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'adviser_id',
        'expertise_id',
        'topic',
        'description',
        'preferred_datetime',
        'meeting_type',
        'status',
        'denial_reason',
        'completion_notes',
        'confirmed_at',
        'scheduled_deletion_at',
        'student_outlook_event_id',
        'adviser_outlook_event_id',
    ];

    protected $casts = [
        'preferred_datetime' => 'datetime',
        'confirmed_at' => 'datetime',
        'scheduled_deletion_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function expertise(): BelongsTo
    {
        return $this->belongsTo(Expertise::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeForAdviser($query, $adviserId)
    {
        return $query->where('adviser_id', $adviserId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}