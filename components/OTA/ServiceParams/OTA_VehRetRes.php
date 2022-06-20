<?php

namespace common\components\OTA\ServiceParams;

use common\components\CarsHelper;
use common\components\OTA\OTAConexion;
use common\models\EntryRateType;
use common\models\Reservation;

/**
 * Class OTA_VehRetRes
 *
 * @package OTA
 *
 */
class OTA_VehRetRes
{
    public $companyCode;

    /**
     * OTA_VehRetRes constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $model Reservation
     * @param $OTAConexion OTAConexion
     * @param bool $envelope
     * @return string
     */
    public function getParameters($model, $OTAConexion, $envelope = true)
    {
        //OTA
        $entryRateType = $OTAConexion->getEntryRateType();
        if ($envelope) {
            //Envelope
            $requestSoap = $OTAConexion->getEnvelope();
            $requestSoap = $OTAConexion->setHeader($requestSoap);
            //Body
            $Body = $requestSoap->addChild('Body', null, $OTAConexion->getNamespaceSoap());
            //Service Name
            switch ($entryRateType) {
                case EntryRateType::UNIDAS_NAME:
                    $OTA_VehAvailRate = $Body->addChild('OtaVehRetRes', '', 'http://www.unidas.com.br/');
                    $otaRQ = $OTA_VehAvailRate->addChild($this->getServiceName() . 'RQ', '', 'http://www.opentravel.org/OTA/2003/05');
                    $otaRQ->addAttribute('Version', 0);
                    break;
                case EntryRateType::LOCALIZA_NAME:
                    $OTA_VehAvailRate = $Body->addChild('OTA_VehRetRes');
                    $OTA_VehAvailRate->addAttribute('xmlns', 'http://tempuri.org/');
                    //Service Name
                    $otaRQ = $OTA_VehAvailRate->addChild($this->getServiceName() . 'RQ');
                    break;
                case EntryRateType::ACE_NAME:
                    $otaRQ = $Body->addChild($this->getServiceName() . 'RQ', '', 'http://www.opentravel.org/OTA/2003/05');
                    $otaRQ->addAttribute('xmlns:xsi',  $OTAConexion->getXsi());
                    $otaRQ->addAttribute('xmlns:xsd',  'http://www.w3.org/2001/XMLSchema');
                    break;
                default:
                    $otaRQ = $Body->addChild($this->getServiceName() . 'RQ', null, $OTAConexion->getXmlns());
            }
        } else {
            if (!empty($OTAConexion->getXsi())) {
                $otaRQ = simplexml_load_string('<' . $this->getServiceName() . 'RQ xmlns="' . $OTAConexion->getXmlns() . '" xmlns:xsi="' . $OTAConexion->getXsi() . '" xsi:schemaLocation="' . $OTAConexion->getXmlns() . ' ' . $this->getServiceName() . 'RQ.xsd" />', 'SimpleXMLElement', 0, $OTAConexion->getNamespaceSoap(), true);
            } else {
                $otaRQ = simplexml_load_string('<' . $this->getServiceName() . 'RQ xmlns="' . $OTAConexion->getXmlns() . '" />', 'SimpleXMLElement', 0, $OTAConexion->getNamespaceSoap(), true);
            }
        }

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
        if ($entryRateType === EntryRateType::LOCALIZA_NAME) {
            $OTAConexion->getPos($otaRQ, true);
        } else {
            $OTAConexion->getPos($otaRQ);
        }

        //VehRetResRQCore
        $VehRetResRQCore = $otaRQ->addChild('VehRetResRQCore');

        //UniqueID
        $UniqueID = $VehRetResRQCore->addChild('UniqueID');
        $UniqueID->addAttribute('ID', $model->rental_confirmation_code);
        if ($entryRateType === EntryRateType::MOVIDA_NAME || $entryRateType === EntryRateType::ACE_NAME) {
            $UniqueID->addAttribute('Type', 14);
        } else if ($entryRateType === EntryRateType::LOCALIZA_NAME) {
            $UniqueID->addAttribute('Type', 14);
            $UniqueID->addAttribute('xmlns', $OTAConexion->getXmlns());
        } else if($entryRateType !== EntryRateType::UNIDAS_NAME) {
            $UniqueID->addAttribute('Type', $OTAConexion->getType());
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
        return 'OTA_VehRetRes';
    }
}