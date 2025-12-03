<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AddressObjInAddress;
use App\Models\AdmAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\AddrObj;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\AddrObjTypes;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\AdmHierarchy;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\Houses;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\HouseTypes;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\MunHierarchy;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\Param;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\ReestrObjects;

class FindController extends Controller
{
    public function findAdm(Request $request)
    {
        $filterBy = $request->input('filter_by');
        $sortBy = $request->input('sort_by');

        $options = [
            'limit_hits' => 15
        ];
        if (is_array($filterBy) && count($filterBy) > 0) {
            $filters = collect();
            foreach ($filterBy as $key => $filter) {
                $filters->push("$key:=$filter");
            }
            $filters = implode(" && ", $filters->values()->toArray());
            $options['filter_by'] = "$filters";
        }

        if (is_array($sortBy) && count($sortBy) > 0) {
            $sortByFilterKey = array_key_first($sortBy);
            $sortByFilterValue = array_shift($sortBy);
            $options['sort_by'] = "$sortByFilterKey:$sortByFilterValue";
        }

        return AdmAddress::search($request->input('search'))
            ->options($options)
            ->get();

        /*
         * Регионы
         */
//        $regions = AddrObj::query()
//            ->where('level', 1)
//            ->where('enddate', '>', now())
//            ->get(); // Уровень 1 — регионы
//
//        foreach ($regions as $region) {
//            $regionFullName = Param::where('objectid', $region->objectid)
//                ->where('typeid', 16) // Офф. наименование
//                ->where('enddate', '>', now())
//                ->first();
//            $regionFullName = $regionFullName->value;
//
//            Region::updateOrCreate(
//                ['objectid' => $region->objectid],
//                [
//                    'objectid' => $region->objectid,
//                    'objectguid' => $region->objectguid,
//                    'name' => $region->name,
//                    'name_full' => $regionFullName,
//                    'typename' => $region->typename,
//                    'updatedate' => $region->updatedate,
//                    'enddate' => $region->enddate,
//                    'isactual' => $region->isactual,
//                    'isactive' => $region->isactive,
//                ]
//            );
//        }
//
//        /*
//         * Муниципальное деление
//         */
//        AddrObj::query()
//            ->where('level', 3)
//            ->where('enddate', '>', now())
//            ->chunk(300, function ($municipalDistricts) {
//                foreach ($municipalDistricts as $municipalDistrict) {
//                    $municipalDistrictFullName = Param::where('objectid', $municipalDistrict->objectid)
//                        ->where('typeid', 16) // Офф. наименование
//                        ->where('enddate', '>', now())
//                        ->first();
//
//                    $municipalDistrictFullName = $municipalDistrictFullName ? $municipalDistrictFullName->value : null;
//
//                    Municipality::updateOrCreate(
//                        ['objectid' => $municipalDistrict->objectid],
//                        [
//                            'objectid' => $municipalDistrict->objectid,
//                            'objectguid' => $municipalDistrict->objectguid,
//                            'name' => $municipalDistrict->name,
//                            'name_full' => $municipalDistrictFullName,
//                            'typename' => $municipalDistrict->typename,
//                            'updatedate' => $municipalDistrict->updatedate,
//                            'enddate' => $municipalDistrict->enddate,
//                            'isactual' => $municipalDistrict->isactual,
//                            'isactive' => $municipalDistrict->isactive,
//                        ]
//                    );
//                }
//            }); // Уровень 3 — Муниципальные/городские округи/районы
//
//        /*
//         * Города
//         */
//        AddrObj::query()
//            ->where('level', 5)
//            ->where('enddate', '>', now())
//            ->chunk(300, function ($cities) {
//                foreach ($cities as $city) {
//                    Municipality::updateOrCreate(
//                        ['objectid' => $city->objectid],
//                        [
//                            'objectid' => $city->objectid,
//                            'objectguid' => $city->objectguid,
//                            'name' => $city->name,
//                            'typename' => $city->typename,
//                            'typename_full' => $this->findFullType($city->typename),
//                            'updatedate' => $city->updatedate,
//                            'enddate' => $city->enddate,
//                            'isactual' => $city->isactual,
//                            'isactive' => $city->isactive,
//                        ]
//                    );
//                }
//            }); // Уровень 3 — Города
    }

    public function getDistrict($objectid, Request $request)
    {
        $address = AdmAddress::where('objectid', $objectid)->firstOrFail();
        $parentObj = AdmAddress::where('objectid', $address->parentobjid)->first();
        if ($parentObj->level === 2) {
            return response()->json($parentObj);
        } else {
            return response()->json($address);
        }
    }
}
