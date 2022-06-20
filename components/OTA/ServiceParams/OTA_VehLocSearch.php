<?php

namespace common\components\OTA\ServiceParams;

use common\components\OTA\OTAConexion;
use common\models\EntryRateType;
use SimpleXMLElement;
use yii\helpers\VarDumper;

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
        switch ($entryRateType) {
            case EntryRateType::LOCALIZA_NAME:
                $ota = $Body->addChild($this->getServiceName(), null, 'http://tempuri.org/');
                $otaRQ = $ota->addChild($this->getServiceName() . 'RQ');
                break;
            case EntryRateType::UNIDAS_NAME:
                $OTA_VehAvailRate = $Body->addChild('OtaVehLocSearch', '', 'http://www.unidas.com.br/');
                $otaRQ = $OTA_VehAvailRate->addChild('OTA_VehLocSearchRQ', '', 'http://www.opentravel.org/OTA/2003/05');
                $otaRQ->addAttribute('Version', 0);
                break;
            default:
                $otaRQ = $Body->addChild($this->getServiceName() . 'RQ', null, $OTAConexion->getXmlns());
        }
        //$otaRQ = new SimpleXMLElement('<' . $this->getServiceName() . 'RQ/>');
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
        switch ($entryRateType) {
            case EntryRateType::UNIDAS_NAME:
                break;
            default:
        }
        //VehLocSearchCriterion
        $VehLocSearchCriterion = $otaRQ->addChild('VehLocSearchCriterion');
        if($entryRateType === EntryRateType::ACE_NAME) {
            $Vendor = $otaRQ->addChild('Vendor');
            $Vendor->addAttribute('Code', 'AC');
        } else {
            //Address
            $Address = $VehLocSearchCriterion->addChild('Address');
            //CityName
            $Address->addChild('CityName', $cityName);
            //CountryName
            $Address->addChild('CountryName', $countryName);
        }

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