<?php

namespace common\components\Ace\ServiceParams;

use common\components\OTA\OTAConexion;
use common\models\EntryRateType;

class OTA_VehLocDetail
{
    /**
     * OTA_VehLocDetail constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $locationCode
     * @param $ID
     * @param $Type
     * @param $namespaceSoap
     * @param $xsi
     * @param $xmlns
     * @param $version
     * @param $Target
     * @return mixed
     */
    public function getParameters($locationCode, $ID, $Type, $namespaceSoap, $xsi, $xmlns, $version, $Target)
    {
        $OTAConexion = new OTAConexion();
        $OTAConexion->setID($ID);
        $OTAConexion->setType($Type);
        $OTAConexion->setXsi($xsi);
        $OTAConexion->setNamespaceSoap($namespaceSoap);
        $OTAConexion->setXmlns($xmlns);
        $OTAConexion->setVersion($version);
        $OTAConexion->setTarget($Target);
        $OTAConexion->setEntryRateType(EntryRateType::ACE_NAME);
        return $OTAConexion->OTA_VehLocDetail($locationCode, 'AC');
    }

    public function getServiceName()
    {
        return 'OTA_VehLocDetail';
    }
}