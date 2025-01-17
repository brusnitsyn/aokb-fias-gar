<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AddressObjInAddress;
use Illuminate\Http\Request;
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

    public function findAdm(Request $request)
    {
        $adms = collect();

        AdmHierarchy::query()
            ->chunk(100, function ($adm) use ($adms) {
                foreach ($adm as $admObj) {
                    $pathPoints = explode('.', $admObj->path);
                    $fullName = collect();
                    $addressParts = collect();

                    foreach ($pathPoints as $pathPoint) {
                        // Определить, что за обьект
                        $reestr = ReestrObjects::query()
                            ->where('objectid', $pathPoint)
                            ->where('isactive', 1)
                            ->select(['levelid'])
                            ->first();

                        switch ($reestr->levelid) {
                            // Субъект РФ
                            case 1: {
                                $entity = $this->findAddrObj($pathPoint);

                                $fullName->push("$entity->name $entity->typename");
                                $addressParts->push([
                                    'addr_obj_id' => $entity->id,
                                ]);
                                break;
                            }
                            /*
                             * Город
                             * Элемент улично-дорожной сети
                             */
                            case 5:
                            case 8: {
                                $entity = $this->findAddrObj($pathPoint);

                                $fullName->push("$entity->typename $entity->name");
                                $addressParts->push([
                                    'addr_obj_id' => $entity->id,
                                ]);
                                break;
                            }
                            // Здание (строение), сооружение
                            case 10: {
                                dd($pathPoint);
                                $entity = $this->findHouse($pathPoint);
//                                $addressParts->push([
//                                  'addr_obj_id' => $entity->id,
//                                ]);
                                $fullName->push("$entity");
                                break;
                            }
                        }

//                        $entity = AddrObj::query()
//                            ->where('objectid', $pathPoint)
//                            ->first();
//
//                        $hasFindObjType = AddrObjTypes::query()
//                            ->where('level', $entity->level)
//                            ->first();
//
//                        if ($hasFindObjType) {
//                            if ($entity->level == 1) {
//                                $fullName->push("$entity->name $entity->typename");
//                            } else {
//                                $addressParts->push([
//                                    'addr_obj_id' => $entity->id,
//                                ]);
//                                $fullName->push("$entity->typename $entity->name");
//                            }
//                        } else {
//                            $entity = Houses::query()
//                                ->where('objectid', $pathPoint)
//                                ->first();
//
//                            if ($entity) {
//                                $fullName->push("$entity->typename $entity->name");
//                            }
//                        }
                    }

                    $address = Address::create([
                        'objectid' => $admObj->objectid,
                        'parentobjid' => $admObj->parentobjid,
                        'path' => $admObj->path,
                        'full_name' => $fullName->implode(', '),
                        'isactive' => $admObj->isactive,
                        'regioncode' => $admObj->regioncode,
                        'citycode' => $admObj->citycode,
                        'placecode' => $admObj->placecode,
                        'plancode' => $admObj->plancode,
                        'streetcode' => $admObj->streetcode,
                        'updatedate' => $admObj->updatedate,
                        'startdate' => $admObj->startdate,
                        'enddate' => $admObj->enddate,
                    ]);

                    $address->addressParts()->createMany($addressParts);
                }
            });

        return response()->json([
            'addresses' => $adms
        ]);
    }
}
