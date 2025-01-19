<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
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

    public function region(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
