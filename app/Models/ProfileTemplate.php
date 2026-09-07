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
        'header_template_id',
        'detail_template_id',
        'footer_template_id',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function headerTemplate()
    {
        return $this->belongsTo(HeaderTemplate::class, 'header_template_id', 'header_template_id');
    }

    public function detailTemplate()
    {
        return $this->belongsTo(DetailTemplate::class, 'detail_template_id', 'detail_template_id');
    }

    public function footerTemplate()
    {
        return $this->belongsTo(FooterTemplate::class, 'footer_template_id', 'footer_template_id');
    }
}
