<?php

namespace micro\components\OTA\ServiceParams;

use micro\components\OTA\OTAConexion;

/**
 * Class OTA_VehLocSearch
 *
 * @package OTA
 *
 */
class OTA_VehLocSearch
{
    public $companyCode;

    /**
     * OTA_VehLocSearch constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $cityName
     * @param $countryName
     * @param $OTAConexion OTAConexion
     * @return string
     */
    public function getParameters($cityName, $countryName, $OTAConexion)
    {
        $entryRateType = $OTAConexion->getEntryRateType();
        //Envelope
        $requestSoap = $OTAConexion->getEnvelope();
        //Body
        $Body = $requestSoap->addChild('Body', null, $OTAConexion->getNamespaceSoap());
        //Service Name
        $otaRQ = $Body->addChild($this->getServiceName() . 'RQ', null, $OTAConexion->getXmlns());
        if (!empty($OTAConexion->getVersion())) {
            $otaRQ->addAttribute('Version', $OTAConexion->getVersion());
        }
        if (!empty($OTAConexion->getEchoToken())) {
            $otaRQ->addAttribute('EchoToken', $OTAConexion->getEchoToken());
        }
        if (!empty($OTAConexion->getTarget())) {
            $otaRQ->addAttribute('Target', $OTAConexion->getTarget());
        }

        //POS
        $OTAConexion->getPos($otaRQ);
        //VehLocSearchCriterion
        $VehLocSearchCriterion = $otaRQ->addChild('VehLocSearchCriterion');
        $Vendor = $otaRQ->addChild('Vendor');
        $Vendor->addAttribute('Code', 'AC');
        if (isset($requestSoap)) {
            $dom = dom_import_simplexml($requestSoap)->ownerDocument;
        } else {
            $dom = dom_import_simplexml($otaRQ)->ownerDocument;
        }
        $dom->formatOutput = true;
        return $dom->saveXML();
        //return $requestSoap->asXML();
    }

    public function getServiceName()
    {
        return 'OTA_VehLocSearch';
    }
}