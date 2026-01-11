<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ApplicationPartner extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'application_partners';
    protected $primaryKey = 'application_id';

    protected $fillable = [
        'application_id',
        'application_name',
        'application_code',
        'token',
    ];

    protected $hidden = [
        'token',
    ];

    public function getAuthPassword()
    {
        return $this->token;
    }
}