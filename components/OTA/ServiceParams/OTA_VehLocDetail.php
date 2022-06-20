<?php

namespace common\components\OTA\ServiceParams;

use common\components\OTA\OTAConexion;
use SimpleXMLElement;

/**
 * Class OTA_VehLocDetail
 *
 * @package OTA
 *
 */
class OTA_VehLocDetail
{
    public $companyCode;

    /**
     * OTA_VehLocDetail constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $locationCode
     * @param string $codeContext
     * @param $OTAConexion OTAConexion
     * @param bool $envelope
     * @return string
     */
    public function getParameters($locationCode, $codeContext, $OTAConexion, $envelope = true)
    {
        //OTA
        if ($envelope) {
            //Envelope
            $requestSoap = $OTAConexion->getEnvelope();
            //Body
            $Body = $requestSoap->addChild('Body', null, $OTAConexion->getNamespaceSoap());
            //Service Name
            $otaRQ = $Body->addChild($this->getServiceName() . 'RQ', null, $OTAConexion->getXmlns());
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

        //POS
        $OTAConexion->getPos($otaRQ);

        //Location
        if (!empty($locationCode)) {
            $Location = $otaRQ->addChild('Location');
            //LocationCode
            $Location->addAttribute('LocationCode', $locationCode);
            //CodeContext
            if (!empty($codeContext)) {
                $Location->addAttribute('CodeContext', $codeContext);
            }
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
        return 'OTA_VehLocDetail';
    }
}