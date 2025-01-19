<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AddressObjInAddress;
use App\Models\AdmAddress;
use Illuminate\Http\Request;
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
    public function __invoke(Request $request)
    {
        $search_string = (string)$request->input('search');
        $searchArray = collect(explode(',', Str::trim($search_string)))->reverse();

        foreach ($searchArray as $search) {
            dd($search);
        }

        $addrs = AddrObj::query()
            ->where('name', 'like', '%' . $search_string . '%')
            ->get();

        $addrs = MunHierarchy::query()
            ->where('objectid', $addrs[0]->objectid)
            ->get();

//        dd($addrs);

        $addrs->map(function ($addr) {
            $addrIds = explode('.', $addr->path);
            $addr->full_name = collect();

            foreach ($addrIds as $addrId) {
                $entity = ReestrObjects::query()
                    ->where('objectid', $addrId)
                    ->select(['levelid', 'isactive'])
                    ->first();

//                dd($entity);

                if ($entity->levelid == 1) {
                    $addrObj = AddrObj::query()
                        ->where('objectid', $addrId)
                        ->select(['name', 'typename'])
                        ->first();

                    $addr->full_name->push("$addrObj->name $addrObj->typename");
                }

                if ($entity->levelid == 3) {
                    $addrObj = AddrObj::query()
                        ->where('objectid', $addrId)
                        ->select(['name', 'typename'])
                        ->first();

                    $addr->full_name->push("$addrObj->typename $addrObj->name");
                }
            }

            $addr->full_name = $addr->full_name->implode(', ');
        });

        return response()->json([
            '' => $addrs
        ]);
    }

    private function findAddrObj(int $objectid)
    {
        return AddrObj::query()
            ->where('objectid', $objectid)
            ->first();
    }

    private function findHouse(int $objectid)
    {
        $house = Houses::query()
            ->where('objectid', $objectid)
            ->first();

        dd($house);

        $houseType = HouseTypes::query()
            ->where('id', $house->housetype)
            ->first();

        return "$houseType->shortname $house->housenum";
    }

    private function findFullType(string $typename)
    {
        return AddrObjTypes::where('typename', $typename)
            ->where('enddate', '>', now())
            ->first();
    }

    public function findAdm(Request $request)
    {
        return AdmAddress::search($request->input('search'))->options([
            'filter_by' => 'level:=5'
        ])
            ->get();
        $adms = collect();

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

        return response()->json([
            'addresses' => $adms
        ]);
    }
}
