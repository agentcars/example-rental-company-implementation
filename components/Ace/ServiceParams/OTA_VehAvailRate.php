<?php
namespace micro\components\Ace\ServiceParams;

use micro\components\OTA\OTAConexion;


class OTA_VehAvailRate
{
    /**
     * OTA_VehAvailRate constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $getDataModel
     * @param $rates
     * @param $ID
     * @param $Type
     * @param $namespaceSoap
     * @param $xsi
     * @param $xmlns
     * @param $version
     * @param $Target
     * @param $RateCategory
     * @return string|string[]
     */
    public function getParameters($getDataModel, $rates, $ID, $Type, $namespaceSoap, $xsi, $xmlns, $version, $Target, $RateCategory)
    {
        $OTAConexion = new OTAConexion();
        $OTAConexion->setID($ID);
        $OTAConexion->setType($Type);
        $OTAConexion->setXsi($xsi);
        $OTAConexion->setNamespaceSoap($namespaceSoap);
        $OTAConexion->setXmlns($xmlns);
        $OTAConexion->setVersion($version);
        $OTAConexion->setTarget($Target);
        $OTAConexion->setRateCategory($RateCategory);
        $OTAConexion->setPickUpLocation($getDataModel['pickUpLocation']);
        $OTAConexion->setReturnLocation($getDataModel['dropOffLocation']);
        //$OTAConexion->setEnvelope($Envelope);
        $OTAConexion->setEntryRateType('Ace');
        return $OTAConexion->OTA_VehAvailRate($getDataModel, $rates);
    }

    public function getServiceName()
    {
        return 'VehAvailRate';
    }
}