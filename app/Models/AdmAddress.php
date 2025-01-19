<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

#[ApiResource]
class AdmAddress extends Model
{
    use Searchable;

    protected $fillable = [
        'objectid',
        'parentobjid',
        'regioncode',
        'path',
        'full_name',
        'level',
        'isactive',
        'updatedate',
        'startdate',
        'enddate',
    ];

    public function toSearchableArray(): array
    {
        return array_merge(
            $this->toArray(),
            [
                // Cast id to string and turn created_at into an int32 timestamp
                // in order to maintain compatibility with the Typesense index definition below
                'id' => (string) $this->id,
                'level' => (integer) $this->level,
                'created_at' => $this->created_at->timestamp,
            ]
        );
    }
}
