<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'comp_tax',
        'profile_id',
        'remark',
        'status',
        'firstname',
        'lastname',
        'email',
        'password',
        'temporary_token',
        'lsp_tax_no',
        'lsp_comp_nmt',
        'lsp',
    ];

    /**
     * Get the user's full name from firstname and lastname (or fallback to name/username).
     */
    public function getNameAttribute(?string $value): string
    {
        $fullName = trim(($this->firstname ?? '') . ' ' . ($this->lastname ?? ''));
        if ($fullName !== '') {
            return $fullName;
        }

        return $value ?: ($this->username ?? '');
    }

    /**
     * Relationship to Profile model.
     */
    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'profile_id');
    }

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
            'status' => 'integer',
        ];
    }

        protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = now();
        });
    } 
}
