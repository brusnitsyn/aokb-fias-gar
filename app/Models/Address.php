<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Address extends Model
{
    use Searchable;

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

    public function toSearchableArray(): array
    {
        return array_merge(
            $this->toArray(),
            [
                // Cast id to string and turn created_at into an int32 timestamp
                // in order to maintain compatibility with the Typesense index definition below
                'id' => (string) $this->id,
                'created_at' => $this->created_at->timestamp,
            ]
        );
    }
}
