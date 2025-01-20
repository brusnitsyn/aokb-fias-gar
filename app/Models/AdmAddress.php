<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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
                'id' => (string) $this->id,

                'objectid' => (integer) $this->objectid,
                'parentobjid' => (integer) $this->parentobjid,
                'regioncode' => (integer) $this->regioncode,
                'path' => (string) $this->path,
                'full_name' => (string) $this->full_name,
                'level' => (integer) $this->level,
                'isactive' => (integer) $this->isactive,
                'updatedate' => Carbon::parse($this->updatedate, config('app.timezone'))->timestamp,
                'startdate' => Carbon::parse($this->startdate, config('app.timezone'))->timestamp,
                'enddate' => Carbon::parse($this->enddate, config('app.timezone'))->timestamp,

                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at->timestamp,
            ]
        );
    }
}
