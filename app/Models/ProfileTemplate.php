<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'profile_templates';
    protected $primaryKey = 'profile_template_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'profile_id',
        'profile_template_name',
        'description',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
