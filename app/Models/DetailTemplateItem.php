<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTemplateItem extends Model
{
    protected $table = 'detail_template_items';

    protected $fillable = [
        'detail_template_id',
        'cell_id',
        'field_name',
        'custom_text',
        'seq',
    ];

    public function detailTemplate()
    {
        return $this->belongsTo(DetailTemplate::class, 'detail_template_id', 'detail_template_id');
    }
}
