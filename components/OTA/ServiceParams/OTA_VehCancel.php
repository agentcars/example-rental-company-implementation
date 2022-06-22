<?php

namespace micro\components\OTA\ServiceParams;

use micro\components\OTA\OTAConexion;

/**
 * Class OTA_VehCancel
 *
 * @package OTA
 *
 */
class OTA_VehCancel
{
    public $companyCode;

    /**
     * OTA_VehCancel constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $lastName
     * @param $confirmationCode
     * @param $OTAConexion OTAConexion
     * @param bool $envelope
     * @return mixed
     */
    public function getParameters($lastName, $confirmationCode, $OTAConexion, $envelope = true)
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
            $otaRQ = $Body->addChild($this->getServiceName() . 'RQ', '', 'http://www.opentravel.org/OTA/2003/05');
            $otaRQ->addAttribute('xmlns:xsi',  $OTAConexion->getXsi());
            $otaRQ->addAttribute('xmlns:xsd',  'http://www.w3.org/2001/XMLSchema');
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
        $OTAConexion->getPos($otaRQ);
        //VehRetResRQCore
        $VehCancelRQCore = $otaRQ->addChild('VehCancelRQCore');
        $VehCancelRQCore->addAttribute('CancelType', 'Cancel');
        //UniqueID
        $UniqueID = $VehCancelRQCore->addChild('UniqueID');
        $UniqueID->addAttribute('ID', $confirmationCode);
        $UniqueID->addAttribute('Type', 14);
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
        return 'OTA_VehCancel';
    }
}