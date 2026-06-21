<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeaderTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'header_templates';
    protected $primaryKey = 'header_template_id';
    
    protected $fillable = [
        'profile_id',
        'header_template_name',
        'description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
