<?php

namespace micro\components\OTA\ServiceParams;

use micro\components\OTA\OTAConexion;

/**
 * Class OTA_VehAvailRate
 *
 * @package OTA
 *
 */
class OTA_VehAvailRate
{
    public $companyCode;

    /**
     * OTA_VehAvailRate constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $parameters
     * @param $OTAConexion OTAConexion
     * @param bool $envelope
     * @return string
     */
    public function getParameters($getDataModel, $ratesArr, $OTAConexion, $envelope = true)
    {
        $entryRateType = $OTAConexion->getEntryRateType();
        $coveragePrefCode = $OTAConexion->getCoveragePrefCode();
        $pickUpDateTime = $getDataModel['pickUpDate'] . 'T' . self::getTimeFormated($getDataModel['pickUpHour']) . ':00';
        $returnDateTime = $getDataModel['dropOffDate'] . 'T' . self::getTimeFormated($getDataModel['dropOffHour']) . ':00';
        $pickUpLocation = $OTAConexion->getPickUpLocation();
        $returnLocation = $OTAConexion->getReturnLocation();
        //rates
        $RateCategory = $OTAConexion->getRateCategory();
        if (is_array($ratesArr) && count($ratesArr) === 1) {
            $rates = $ratesArr[0];
        } else if (is_array($ratesArr) && count($ratesArr) > 1) {
            $rate_type_id = [];
            if ($RateCategory === '16') {
                $rate_type_id[] = 1;//JUST_CAR
            } else if ($RateCategory === '12') {
                $rate_type_id[] = 3;//SUPER_PROTECTION
                $rate_type_id[] = 2;//BASIC_PROTECTION
                $rate_type_id[] = 7;//BASIC_PROTECTION_EU
            }
            foreach ($ratesArr as $rate) {
                if (in_array($rate['rate_type_id'], $rate_type_id)) {
                    $rates = $rate;
                }
            }
        }
        //OTA
        if ($envelope) {
            //Envelope
            $requestSoap = $OTAConexion->getEnvelope();
            $requestSoap = $OTAConexion->setHeader($requestSoap);
            //Body
            $Body = $requestSoap->addChild('Body', null, $OTAConexion->getNamespaceSoap());
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
        if (!empty($OTAConexion->getEchoToken())) {
            $otaRQ->addAttribute('EchoToken', $OTAConexion->getEchoToken());
        }
        if (!empty($OTAConexion->getTarget())) {
            $otaRQ->addAttribute('Target', $OTAConexion->getTarget());
        }
        //POS
        $OTAConexion->getPos($otaRQ);
        //VehAvailRQCore
        $VehAvailRQCore = $otaRQ->addChild('VehAvailRQCore');
        //VehRentalCore
        $VehRentalCore = $VehAvailRQCore->addChild('VehRentalCore');
        $VehRentalCore->addAttribute('PickUpDateTime', $pickUpDateTime);
        $VehRentalCore->addAttribute('ReturnDateTime', $returnDateTime);
        //PickUpLocation
        $PickUpLocation = $VehRentalCore->addChild('PickUpLocation');
        $PickUpLocation->addAttribute('LocationCode', $pickUpLocation);
        if (strlen($pickUpLocation) === 3) {
            $PickUpLocation->addAttribute('CodeContext', 'IATA');
        } else {
            $PickUpLocation->addAttribute('CodeContext', 'AC');
        }
        //ReturnLocation
        $ReturnLocation = $VehRentalCore->addChild('ReturnLocation');
        $ReturnLocation->addAttribute('LocationCode', $returnLocation);
        if (strlen($returnLocation) === 3) {
            $ReturnLocation->addAttribute('CodeContext', 'IATA');
        } else {
            $ReturnLocation->addAttribute('CodeContext', 'AC');
        }

        if (isset($getDataModel['sippCode']) && !empty($getDataModel['sippCode'])) {
            $VehPrefs = $VehAvailRQCore->addChild('VehPrefs');
            $VehPref = $VehPrefs->addChild('VehPref');
            $VehPref->addAttribute('Code', $getDataModel['sippCode']);
            $VehPref->addAttribute('CodeContext', 'SIPP');
        }
        //RateQualifier
        $RateQualifier = $VehAvailRQCore->addChild('RateQualifier');
        $RateQualifier->addAttribute('PromotionCode', $rates['code']);
        $RateQualifier->addAttribute('RateCategory', $RateCategory);//16 = Retail/Non Inclusive and 12 = inclusive

        if (isset($requestSoap)) {
            $dom = dom_import_simplexml($requestSoap)->ownerDocument;
        } else {
            $dom = dom_import_simplexml($otaRQ)->ownerDocument;
        }
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    public function getDiscountCodes($rates, $completeResponse = false)
    {
        $discountCodes = [];
        foreach ($rates as $key => $value) {
            foreach ($value['discountCodes'] as $code) {
                if ($completeResponse) {
                    $discountCodes[] = $code;
                } else {
                    $discountCodes = $code['code'];
                }
            }
        }
        if (empty($discountCodes)) {
            return '';
        }
        return $discountCodes;
    }

    public function getServiceName()
    {
        return 'OTA_VehAvailRate';
    }

    public static function getTimeFormated($time)
    {
        $hour1 = $time[0] ?? '0';
        $hour2 = $time[1] ?? '0';
        $minute1 = $time[2] ?? '0';
        $minute2 = $time[3] ?? '0';
        return $hour1 . $hour2 . ':' . $minute1 . $minute2;
    }
}