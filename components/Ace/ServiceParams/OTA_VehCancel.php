<?php

namespace micro\components\Ace\ServiceParams;

use micro\components\OTA\OTAConexion;

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
     * @param $lastName
     * @param $confirmationCode
     * @param $ID
     * @param $Type
     * @return string
     */
    public function getParameters($lastName, $confirmationCode, $ID, $Type, $namespaceSoap, $xsi, $xmlns, $version, $Target)
    {
        $OTAConexion = new OTAConexion();
        $OTAConexion->setID($ID);
        $OTAConexion->setType($Type);
        $OTAConexion->setTarget($Target);
        $OTAConexion->setVersion($version);
        $OTAConexion->setXsi($xsi);
        $OTAConexion->setXmlns($xmlns);
        $OTAConexion->setNamespaceSoap($namespaceSoap);
        $OTAConexion->setEntryRateType('Ace');
        return $OTAConexion->OTA_VehCancel($lastName, $confirmationCode);
    }

    public function getServiceName()
    {
        return 'OTA_VehCancel';
    }
}