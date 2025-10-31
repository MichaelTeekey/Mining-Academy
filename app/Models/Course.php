<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * Attributes that can be mass-assigned.
     */
    protected $fillable = [
        'title',
        'description',
        'price',
        'is_free',
        'status',
        'instructor_id',
    ];

    /**
     * Attribute casting for consistency.
     */
    protected $casts = [
        'is_free' => 'boolean',
        'price' => 'float',
    ];

    /**
     * Relationships
     */

    // Instructor (owner of the course)
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Versions (historical snapshots)
    public function courseVersions()
    {
        return $this->hasMany(CourseVersion::class);
    }

    // Runs (individual session offerings)
    public function courseRuns()
    {
        return $this->hasMany(CourseRun::class);
    }

    // Enrollments (via course runs)
    public function enrollments()
    {
        return $this->hasManyThrough(Enrollment::class, CourseRun::class);
    }

    // Nested data: Course → Modules → Lessons → Media
    public function modules()
    {
        return $this->hasManyThrough(Module::class, CourseVersion::class);
    }

    /**
     * Scopes — for easy filtering
     */

    // Filter free courses
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    // Filter paid courses
    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    // Filter active courses
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Accessors
     */

    // Human-readable status
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'draft');
    }

    // Price display (handles free/paid)
    public function getDisplayPriceAttribute(): string
    {
        return $this->is_free ? 'Free' : '$' . number_format($this->price, 2);
    }
}
