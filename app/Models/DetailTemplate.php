<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'detail_templates';
    protected $primaryKey = 'detail_template_id';
    
    protected $fillable = [
        'profile_id',
        'detail_template_name',
        'description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
