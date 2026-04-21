<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'form_templates';
    protected $primaryKey = 'template_id';
    protected $fillable = [
        'template_name',
        'description',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // public function form_template_details()
    // {
    //     return $this->hasMany(FormTemplateDetail::class, 'template_id', 'template_id');
    // }
}
