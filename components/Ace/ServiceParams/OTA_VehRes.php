<?php

namespace micro\components\Ace\ServiceParams;

use micro\components\OTA\OTAConexion;

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
     * @param $reservation
     * @param $ID
     * @param $Type
     * @param $namespaceSoap
     * @param $xsi
     * @param $xmlns
     * @param $version
     * @param $Target
     * @return string
     */
    public function getParameters($reservation, $ID, $Type, $namespaceSoap, $xsi, $xmlns, $version, $Target)
    {
        $OTAConexion = new OTAConexion();
        $OTAConexion->setID($ID);
        $OTAConexion->setType($Type);
        $OTAConexion->setTarget($Target);
        $OTAConexion->setVersion($version);
        $OTAConexion->setXsi($xsi);
        $OTAConexion->setXmlns($xmlns);
        $OTAConexion->setNamespaceSoap($namespaceSoap);
        $OTAConexion->setPickUpLocation($reservation['location_pickup']);
        $OTAConexion->setReturnLocation($reservation['location_dropoff']);
        $OTAConexion->setEntryRateType('Ace');
        return $OTAConexion->OTA_VehRes($reservation);
    }

    public function getServiceName()
    {
        return 'OTA_VehRes';
    }
}