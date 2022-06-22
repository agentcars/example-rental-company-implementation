<?php

namespace micro\components\OTA\ServiceParams;

use micro\components\OTA\OTAConexion;

/**
 * Class OTA_VehRes
 *
 * @package OTA
 *
 */
class OTA_VehRes
{
    public $companyCode;

    /**
     * OTA_VehRes constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $model
     * @param $OTAConexion OTAConexion
     * @param bool $envelope
     * @return string
     */
    public function getParameters($reservation, $OTAConexion, $envelope = true)
    {
        $entryRateType = $OTAConexion->getEntryRateType();
        $additionals = $reservation['additionals'];
        $additional_information = $reservation['additional_information'];
        $pickUpDateTime = $reservation['pickup_date'] . 'T' . self::getTimeFormated($reservation['pickup_hour']) . ':00';
        $returnDateTime = $reservation['dropoff_date'] . 'T' . self::getTimeFormated($reservation['dropoff_hour']) . ':00';
        $pickUpLocation = $OTAConexion->getPickUpLocation();
        $returnLocation = $OTAConexion->getReturnLocation();
        //OTA
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
        //VehResRQCore
        $VehResRQCore = $otaRQ->addChild('VehResRQCore');
        //VehRentalCore
        $VehRentalCore = $VehResRQCore->addChild('VehRentalCore');
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
        //Customer
        $Customer = $VehResRQCore->addChild('Customer');
        //Primary
        $Primary = $Customer->addChild('Primary');
        //PersonName
        $PersonName = $Primary->addChild('PersonName');
        $PersonName->addChild('GivenName', $reservation['first_name']);
        $PersonName->addChild('Surname', $reservation['last_name']);
        //Email
        $Primary->addChild('Email', $reservation['email']);
        //SpecialEquipPrefs
        $getSpecialEquipPrefs = $this->getSpecialEquipPrefs($additionals);
        $countSpecialEquipPrefs = count($getSpecialEquipPrefs);
        if ($countSpecialEquipPrefs > 0) {
            $SpecialEquipPrefs = $VehResRQCore->addChild('SpecialEquipPrefs');
            for ($i = 0; $i < $countSpecialEquipPrefs; $i++) {
                $SpecialEquipPref[$i] = $SpecialEquipPrefs->addChild('SpecialEquipPref');
                $SpecialEquipPref[$i]->addAttribute('EquipType', $getSpecialEquipPrefs[$i]);
            }
        }
        //VehResRQInfo
        $VehResRQInfo = $otaRQ->addChild('VehResRQInfo');
        $Reference = $VehResRQInfo->addChild('Reference');
        $Reference->addAttribute('ID', $additional_information['ReferenceID']);
        $Reference->addAttribute('Type', $additional_information['ReferenceType']);
        $RentalPaymentPref = $VehResRQInfo->addChild('RentalPaymentPref');
        $PaymentAmount = $RentalPaymentPref->addChild('PaymentAmount');
        $PaymentAmount->addAttribute('Amount', $additional_information['Amount']);
        $PaymentAmount->addAttribute('CurrencyCode', $additional_information['CurrencyCode']);

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
        return 'OTA_VehRes';
    }

    public function getSpecialEquipPrefs($additionals)
    {
        $specialEquipPrefs = [];
        foreach ($additionals as $code => $additional) {
            //Child toddler seat
            if ($code === 'cst' && $additional['value'] > 0) {
                $specialEquipPrefs[] = 8;
            }
            //Booster seat
            if ($code === 'cbs' && $additional['value'] > 0) {
                $specialEquipPrefs[] = 9;
            }
            //GPS
            if ($code === 'gps' && $additional['value'] > 0) {
                $specialEquipPrefs[] = 13;
            }
            //Ski rack
            if ($code === 'sky' && $additional['value'] > 0) {
                $specialEquipPrefs[] = 4;
            }
        }
        return $specialEquipPrefs;
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