<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysfieldEx extends Model
{
    protected $connection = 'print';
    protected $table = 'sysfield_ex';
    protected $primaryKey = 'recno';
    public $timestamps = false;

    protected $guarded = [];
}
