<?php

namespace common\components\Ace\ServiceParams;

use common\components\OTA\OTAConexion;
use common\models\EntryRateType;

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
     * @param $model
     * @param $ID
     * @param $Type
     * @param $namespaceSoap
     * @param $xsi
     * @param $xmlns
     * @param $version
     * @param $Target
     * @return mixed
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
        return $OTAConexion->OTA_VehRetRes($model);
    }

    public function getServiceName()
    {
        return 'OTA_VehRetRes';
    }
}