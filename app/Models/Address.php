<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'objectid',
        'parentobjid',
        'path',
        'full_name',
        'isactive',
        'regioncode',
        'citycode',
        'placecode',
        'plancode',
        'streetcode',
        'updatedate',
        'startdate',
        'enddate',
    ];

    public function addressParts()
    {
        return $this->hasMany(AddressObjInAddress::class);
    }
}
