<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'region_id',
        'objectid',
        'objectguid',
        'name',
        'name_full',
        'typename',
        'typename_full',
        'updatedate',
        'enddate',
        'isactual',
        'isactive',
    ];
}
