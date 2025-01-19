<?php

namespace App\Models;

use Faker\Provider\sv_SE\Municipality;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'objectid',
        'objectguid',
        'name',
        'name_full',
        'typename',
        'updatedate',
        'enddate',
        'isactual',
        'isactive',
    ];

    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }
}
