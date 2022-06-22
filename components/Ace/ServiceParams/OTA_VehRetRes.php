<?php

namespace micro\components\Ace\ServiceParams;

use micro\components\OTA\OTAConexion;

/**
 * Class OTA_VehRetRes
 *
 * @package Ace
 */
class OTA_VehRetRes
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
     * @param $namespaceSoap
     * @param $xsi
     * @param $xmlns
     * @param $version
     * @param $Target
     * @return mixed
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
        return $OTAConexion->OTA_VehRetRes($lastName, $confirmationCode);
    }

    public function getServiceName()
    {
        return 'OTA_VehRetRes';
    }
}