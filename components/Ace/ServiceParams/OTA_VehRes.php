<?php

namespace common\components\Ace\ServiceParams;

use common\components\OTA\OTAConexion;
use common\models\EntryRateType;
use common\models\LocationList;
use common\models\Reservation;

/**
 * Class OTA_VehRes
 *
 * @package Ace
 */
class OTA_VehRes
{
    /**
     * OTAVehRes constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param Reservation $model
     * @param $ID
     * @param $Type
     * @param $namespaceSoap
     * @param $xsi
     * @param $xmlns
     * @param $version
     * @param $Target
     * @return string
     */
    public function getParameters($model, $ID, $Type, $namespaceSoap, $xsi, $xmlns, $version, $Target)
    {
        $OTAConexion = new OTAConexion();
        $OTAConexion->setID($ID);
        $OTAConexion->setType($Type);
        $OTAConexion->setTarget($Target);
        $OTAConexion->setVersion($version);
        $OTAConexion->setXsi($xsi);
        $OTAConexion->setXmlns($xmlns);
        $OTAConexion->setNamespaceSoap($namespaceSoap);
        $locations = LocationList::checkAndGetIATALocationsToOTA(
            ['pickUpLocation' => $model->location_pickup, 'dropOffLocation' => $model->location_dropoff],
            'AC', true, true);
        $OTAConexion->setPickUpLocation(!empty($locations['pickUpLocation']) ? $locations['pickUpLocation'] :
            $model->location_pickup);
        $OTAConexion->setReturnLocation(!empty($locations['dropOffLocation']) ? $locations['dropOffLocation'] :
            $model->location_dropoff);
        $OTAConexion->setEntryRateType(EntryRateType::ACE_NAME);
        return $OTAConexion->OTA_VehRes($model);
    }

    public function getServiceName()
    {
        return 'OTA_VehRes';
    }
}