<?php

namespace App\Jobs;

use App\Models\AdmAddress;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\AddrObj;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\AddrObjTypes;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\AdmHierarchy;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\Apartments;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\ApartmentTypes;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\Houses;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\HouseTypes;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\ReestrObjects;
use Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\Steads;

class CreateAdmAddresses implements ShouldQueue
{
    use Queueable;

    private Collection $adms;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $adms)
    {
        $this->adms = $adms;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->adms as $admObj) {
            $pathPoints = collect(explode('.', $admObj->path));
            $fullName = collect();
            $addressParts = collect();
            $level = 0;

            foreach ($pathPoints as $pathPoint) {
                // Определить, что за обьект
                $reestr = ReestrObjects::query()
                    ->where('objectid', $pathPoint)
                    ->select(['levelid'])
                    ->first();

                switch ($reestr->levelid) {
                    /*
                     * Субъект РФ
                     * Административный район
                     */
                    case 1:
                    case 2: {
                        $entity = $this->findAddrObj($pathPoint);

                        $fullName->push("$entity->name $entity->typename");
                        break;
                    }
                    /*
                     * Город
                     * Населенный пункт
                     * Элемент улично-дорожной сети
                     * Микрорайон
                     */
                    case 5:
                    case 6:
                    case 8:
                    case 16: {
                        $entity = $this->findAddrObj($pathPoint);
                        $fullName->push("$entity->typename $entity->name");
                        break;
                    }
                    case 9: {
                        $entity = $this->findStead($pathPoint);
                        $fullName->push($entity);
                        break;
                    }
                    // Здание (строение), сооружение
                    case 10: {
                        $entity = $this->findHouse($pathPoint);
                        $fullName->push("$entity");
                        break;
                    }
                    // Здание (строение), сооружение
                    case 11: {
                        $entity = $this->findApartment($pathPoint);
                        $fullName->push("$entity");
                        break;
                    }
                }

                if ($pathPoints->last() === $pathPoint) {
                    $level = $reestr->levelid;
                }
            }

            AdmAddress::updateOrCreate(
                ['objectid' => $admObj->objectid],
                [
                    'objectid' => $admObj->objectid,
                    'parentobjid' => $admObj->parentobjid,
                    'regioncode' => $admObj->regioncode,
                    'path' => $admObj->path,
                    'full_name' => $fullName->implode(', '),
                    'level' => $level,
                    'isactive' => $admObj->isactive,
                    'updatedate' => $admObj->updatedate,
                    'startdate' => $admObj->startdate,
                    'enddate' => $admObj->enddate,
                ]
            );
        }
    }

    private function findAddrObj(int $objectid): AddrObj
    {
        return AddrObj::where('objectid', $objectid)
            ->first();
    }

    private function findStead(int $objectid): string
    {
        $stead = Steads::where('objectid', $objectid)
            ->first();

        if (!$stead) {
            return "";
        }

        $steadType = AddrObjTypes::where('level', 9)
            ->first();

        if (!$steadType) {
            return "$stead->number";
        }

        return "$steadType->shortname $stead->number";
    }

    private function findHouse(int $objectid): string
    {
        $house = Houses::where('objectid', $objectid)
            ->first();

        if (!$house) {
            return "";
        }

        $houseType = HouseTypes::where('id', $house->housetype)
            ->first();

        if (!$houseType) {
            return "$house->housenum";
        }

        return "$houseType->shortname $house->housenum";
    }

    private function findApartment(int $objectid): string
    {
        $apartment = Apartments::where('objectid', $objectid)
            ->first();

        if (!$apartment) {
            return "";
        }

        $apartmentType = ApartmentTypes::where('id', $apartment->aparttype)
            ->first();

        if (!$apartmentType) {
            return "$apartment->number";
        }

        return "$apartmentType->shortname $apartment->number";
    }

    private function findFullType(string $typename)
    {
        return AddrObjTypes::where('typename', $typename)
            ->where('enddate', '>', now())
            ->first();
    }
}
