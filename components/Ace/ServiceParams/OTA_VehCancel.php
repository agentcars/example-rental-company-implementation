<?php

namespace common\components\Ace\ServiceParams;

use common\components\OTA\OTAConexion;
use common\models\EntryRateType;
use common\models\Reservation;

/**
 * Class OTA_VehCancel
 *
 * @package Ace
 */
class OTA_VehCancel
{
    /**
     * OTAVehRes constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $model Reservation
     * @param $ID
     * @param $Type
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
        $OTAConexion->setEntryRateType(EntryRateType::ACE_NAME);
        return $OTAConexion->OTA_VehCancel($model);
    }

    public function getServiceName()
    {
        return 'OTA_VehCancel';
    }
}