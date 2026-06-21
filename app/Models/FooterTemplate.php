<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FooterTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'footer_templates';
    protected $primaryKey = 'footer_template_id';
    
    protected $fillable = [
        'profile_id',
        'footer_template_name',
        'description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
