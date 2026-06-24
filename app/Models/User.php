<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    // ── Override: Kirim email reset password branded Skolah.com ───────────────
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // ── Override: Kirim email verifikasi branded Skolah.com ───────────────
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    // ── Fillable ──────────────────────────────────────────────────────────────
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'role',
        'is_active',
        'is_public',
        'is_verified',
        'suspended_at',
        'must_change_password',
        'has_seen_onboarding',
        'seen_onboarding_pages',
    ];

    // ── Hidden ────────────────────────────────────────────────────────────────
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ── Casts ─────────────────────────────────────────────────────────────────
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_verified'       => 'boolean',
            'is_active'         => 'boolean',
            'is_public'         => 'boolean',
            'suspended_at'         => 'datetime',
            'bio'                  => 'encrypted',
            'must_change_password' => 'boolean',
            'has_seen_onboarding'  => 'boolean',
            'seen_onboarding_pages' => 'array',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function bootcamps(): HasMany
    {
        return $this->hasMany(Bootcamp::class, 'instructor_id');
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function bootcampRegistrations(): HasMany
    {
        return $this->hasMany(BootcampRegistration::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function bookOrders(): HasMany
    {
        return $this->hasMany(BookOrder::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }

    public function activeMembership(): HasOne
    {
        return $this->hasOne(UserMembership::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latestOfMany();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function instructorActivities(): HasMany
    {
        return $this->hasMany(InstructorActivity::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeInstructors($query)
    {
        return $query->where('role', 'instructor');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        return avatarUrl($this->avatar, $this->name);
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function getIsInstructorAttribute(): bool
    {
        return $this->role === 'instructor';
    }

    public function getIsSuspendedAttribute(): bool
    {
        return ! is_null($this->suspended_at);
    }

    public function getHasActiveMembershipAttribute(): bool
    {
        return $this->activeMembership()->exists();
    }
}
