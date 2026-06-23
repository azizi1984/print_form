<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderTemplateItem extends Model
{
    protected $table = 'header_template_items';
    
    protected $fillable = [
        'header_template_id',
        'cell_id',
        'field_name',
        'seq',
    ];

    public function headerTemplate()
    {
        return $this->belongsTo(HeaderTemplate::class, 'header_template_id', 'header_template_id');
    }
}
