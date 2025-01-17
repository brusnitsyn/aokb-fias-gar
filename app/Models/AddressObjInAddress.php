<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\AddrObj;

class AddressObjInAddress extends Model
{
    protected $fillable = [
        'address_id',
        'addr_obj_id'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function addressObj()
    {
        return $this->belongsTo(AddrObj::class);
    }
}
