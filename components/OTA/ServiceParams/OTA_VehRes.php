<?php

namespace common\components\OTA\ServiceParams;

use common\components\CarsHelper;
use common\components\OTA\OTAConexion;
use common\models\EntryRateType;
use common\models\Reservation;

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
     * @param $model Reservation
     * @param $OTAConexion OTAConexion
     * @param bool $envelope
     * @return string
     */
    public function getParameters($model, $OTAConexion, $envelope = true)
    {
        $entryRateType = $OTAConexion->getEntryRateType();
        $additionals = $model->additionals;
        $additional_information = $model->additional_information;
        $pickUpDateTime = $model->pickup_date . 'T' . CarsHelper::getTimeFormated($model->pickup_hour) . ':00';
        $returnDateTime = $model->dropoff_date . 'T' . CarsHelper::getTimeFormated($model->dropoff_hour) . ':00';
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
            switch ($entryRateType) {
                case EntryRateType::LOCALIZA_NAME:
                    $OTA_VehAvailRate = $Body->addChild('OTA_VehRes');
                    $OTA_VehAvailRate->addAttribute('xmlns', 'http://tempuri.org/');
                    $otaRQ = $OTA_VehAvailRate->addChild($this->getServiceName() . 'RQ');
                    break;
                case EntryRateType::UNIDAS_NAME:
                    $OTA_VehAvailRate = $Body->addChild('OtaVehRes', '', 'http://www.unidas.com.br/');
                    $otaRQ = $OTA_VehAvailRate->addChild($this->getServiceName() . 'RQ', '', 'http://www.opentravel.org/OTA/2003/05');
                    $otaRQ->addAttribute('Version', 0);
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
        if ($entryRateType === 'Localiza') {
            $OTAConexion->getPos($otaRQ, true);
        } else {
            $OTAConexion->getPos($otaRQ);
        }

        //VehResRQCore
        $VehResRQCore = $otaRQ->addChild('VehResRQCore');
        if ($entryRateType === EntryRateType::BLUEBIRD_NAME) {
            $VehResRQCore->addAttribute('Status', 'Available');
        }
        //VehRentalCore
        $VehRentalCore = $VehResRQCore->addChild('VehRentalCore');
        $VehRentalCore->addAttribute('PickUpDateTime', $pickUpDateTime);
        $VehRentalCore->addAttribute('ReturnDateTime', $returnDateTime);
        if ($entryRateType === 'Localiza') {
            $VehRentalCore->addAttribute('OneWayIndicator', 'true');
            $VehRentalCore->addAttribute('xmlns', $OTAConexion->getXmlns());
        }
        //PickUpLocation
        $PickUpLocation = $VehRentalCore->addChild('PickUpLocation');
        $PickUpLocation->addAttribute('LocationCode', $pickUpLocation);
        if (strlen($pickUpLocation) === 3) {
            $PickUpLocation->addAttribute('CodeContext', 'IATA');
        } else if ($entryRateType === EntryRateType::ACE_NAME) {
            $PickUpLocation->addAttribute('CodeContext', 'AC');
        }
        //ReturnLocation
        $ReturnLocation = $VehRentalCore->addChild('ReturnLocation');
        $ReturnLocation->addAttribute('LocationCode', $returnLocation);
        if (strlen($returnLocation) === 3) {
            $ReturnLocation->addAttribute('CodeContext', 'IATA');
        } else if ($entryRateType === EntryRateType::ACE_NAME) {
            $ReturnLocation->addAttribute('CodeContext', 'AC');
        }

        //Customer
        $Customer = $VehResRQCore->addChild('Customer');
        if ($entryRateType === 'Localiza') {
            $Customer->addAttribute('xmlns', $OTAConexion->getXmlns());
        }
        //Primary
        $Primary = $Customer->addChild('Primary');
        //PersonName
        $PersonName = $Primary->addChild('PersonName');
        $PersonName->addChild('GivenName', $model->first_name);
        $PersonName->addChild('Surname', $model->last_name);
        //Email
        $Primary->addChild('Email', $model->email);
        if ($entryRateType === 'Localiza') {
            $CitizenCountryName = $Primary->addChild('CitizenCountryName');
            $CitizenCountryName->addAttribute('Code', $model->source);
        }
        //PaymentForm
        switch ($entryRateType) {
            case 'Movida':
                $PaymentForm = $Primary->addChild('PaymentForm');
                $Voucher = $PaymentForm->addChild('Voucher');
                if ($model->type == 0) {
                    $Voucher->addAttribute('ValueType', 'FPF');
                } else {
                    $Voucher->addAttribute('ValueType', 'INV98');
                }
                break;
            case EntryRateType::UNIDAS_NAME:
                $PaymentForm = $Primary->addChild('PaymentForm');
                $Voucher = $PaymentForm->addChild('Voucher');
                $Voucher->addAttribute('ValueType', 'FPA');
                break;
        }
        //Additional Info
        if ($entryRateType === EntryRateType::MOVIDA_NAME && !empty($model->cd_number) && $model->cd_number === "OUT21CONDUTOR") {
            //Additional
            $Additional = $Customer->addChild('Additional');
            //PersonName
            $PersonNameA = $Additional->addChild('PersonName');
            $PersonNameA->addChild('GivenName', $model->first_name);
            $PersonNameA->addChild('Surname', $model->last_name);
        }
        //CustomerID
//        $CustomerID = $Primary->addChild('CustomerID');
//        $CustomerID->addAttribute('ID', '123456');
//        $CustomerID->addAttribute('Type', '1');

        switch($entryRateType) {
            case EntryRateType::BLUEBIRD_NAME:
                $VehPrefs = $VehResRQCore->addChild('VehPrefs');
                $VehPref = $VehPrefs->addChild('VehPref');
                $VehClass = $VehPref->addChild('VehClass');
                $VehClass->addAttribute('Size', $model->sipp_code);
                break;
            case EntryRateType::LOCALIZA_NAME:
                $VehPref = $VehResRQCore->addChild('VehPref');
                if (isset($additional_information['FuelType'])) {
                    $VehPref->addAttribute('FuelType', $additional_information['FuelType']);
                }
                if (isset($additional_information['DriveType'])) {
                    $VehPref->addAttribute('DriveType', $additional_information['DriveType']);
                }
                if (isset($additional_information['AirConditionInd'])) {
                    $VehPref->addAttribute('AirConditionInd', $additional_information['AirConditionInd']);
                }
                if (isset($additional_information['TransmissionType'])) {
                    $VehPref->addAttribute('TransmissionType', $additional_information['TransmissionType']);
                }
                if (isset($additional_information['VendorCarType'])) {
                    $VehPref->addAttribute('VendorCarType', $additional_information['VendorCarType']);
                }
                $VehPref->addAttribute('xmlns', $OTAConexion->getXmlns());
                $VehType = $VehPref->addChild('VehType');
                $VehType->addAttribute('VehicleCategory', $additional_information['VehicleCategory']);
                $VehType->addAttribute('DoorCount', $additional_information['DoorCount']);
                $VehClass = $VehPref->addChild('VehClass');
                $VehClass->addAttribute('Size', $additional_information['Size']);
                break;
            case EntryRateType::UNIDAS_NAME:
                $VehPref = $VehResRQCore->addChild('VehPref');
                $VehPref->addAttribute('Code', $model->sipp_code);
                $VehPref->addAttribute('CodeContext', $model->sipp_code);
                break;
            case EntryRateType::ACE_NAME:
                break;
            default:
                $VehPref = $VehResRQCore->addChild('VehPref');
                $VehMakeModel = $VehPref->addChild('VehMakeModel');
                $VehMakeModel->addAttribute('Code', $model->sipp_code);
                $VehMakeModel->addAttribute('Name', 'N/A');
        }

        //DriverType
        if ($entryRateType !== EntryRateType::LOCALIZA_NAME && $entryRateType !== EntryRateType::ACE_NAME) {
            $age = ($model->age === '1') ? 26 : 24;
            $DriverType = $VehResRQCore->addChild('DriverType');
            $DriverType->addAttribute('Age', $age);
            if ($entryRateType === EntryRateType::UNIDAS_NAME) {
                $DriverType->addAttribute('CodeContext', 'Unidas');
            }
        }

        //RateQualifier
        if ($entryRateType !== EntryRateType::ACE_NAME) {
            $RateQualifier = $VehResRQCore->addChild('RateQualifier');
            if ($entryRateType === EntryRateType::LOCALIZA_NAME) {
                $RateQualifier->addAttribute('RateQualifier', $additional_information['RateQualifier']);
                $RateQualifier->addAttribute('RateCategory', $additional_information['RateCategory']);
                $RateQualifier->addAttribute('xmlns', $OTAConexion->getXmlns());
            } else {
                $RateQualifier->addAttribute('RateQualifier', $model->rate_code);
                if (isset($additional_information['VendorRateID'])) {
                    $RateQualifier->addAttribute('VendorRateID', $additional_information['VendorRateID']);
                }
            }
            if ($entryRateType === EntryRateType::MOVIDA_NAME || $entryRateType === EntryRateType::UNIDAS_NAME) {
                $RateQualifier->addAttribute('PromotionCode', $model->cd_number);
            }
        }

        //VehicleCharges
        //SpecialEquipPrefs
        $getSpecialEquipPrefs = $this->getSpecialEquipPrefs($additionals);
        $countSpecialEquipPrefs = count($getSpecialEquipPrefs);
        if ($countSpecialEquipPrefs > 0 && $entryRateType !== EntryRateType::UNIDAS_NAME) {
            $SpecialEquipPrefs = $VehResRQCore->addChild('SpecialEquipPrefs');
            for ($i = 0; $i < $countSpecialEquipPrefs; $i++) {
                $SpecialEquipPref[$i] = $SpecialEquipPrefs->addChild('SpecialEquipPref');
                $SpecialEquipPref[$i]->addAttribute('EquipType', $getSpecialEquipPrefs[$i]);
                if ( $entryRateType === EntryRateType::UNIDAS_NAME){
                    $SpecialEquipPref[$i]->addAttribute('Quantity', 1);
                }
            }
        }

        //UniqueID
        switch ($entryRateType) {
            case EntryRateType::MOVIDA_NAME:
                $UniqueID = $VehResRQCore->addChild('UniqueID');
                $UniqueID->addAttribute('ID', $model->rate_identifier);
                $UniqueID->addAttribute('Type', $additional_information['CoverageType'] ?? '24');
                break;
            case EntryRateType::BLUEBIRD_NAME:
            case EntryRateType::LOCALIZA_NAME:
            case EntryRateType::UNIDAS_NAME:
                break;
        }

        //VehResRQInfo
        switch ($entryRateType) {
            case EntryRateType::BLUEBIRD_NAME:
                $VehResRQInfo = $otaRQ->addChild('VehResRQInfo');
                //TourInfo
                $TourInfo = $VehResRQInfo->addChild('TourInfo');
                $TourInfo->addAttribute('TourNumber', '11111');
                $TourOperator = $TourInfo->addChild('TourOperator');
                $TourOperator->addAttribute('CompanyShortName', $model->rate->code);
                //TPA_Extension
                $TPA_Extension = $VehResRQInfo->addChild('TPA_Extension');
                $TPA_Extension->addAttribute('mustProcess', '1');
                $SourceOfBusiness = $TPA_Extension->addChild('SourceOfBusiness');
                $SourceOfBusiness->addAttribute('SourceCode', '600');
                $SourceOfBusiness->addAttribute('Referral', '600-15');
                $SourceOfBusiness->addAttribute('AgentID', 'RENTINGC');
                //RentalPaymentPref
                $RentalPaymentPref = $VehResRQInfo->addChild('RentalPaymentPref');
                $DirectBill = $RentalPaymentPref->addChild('DirectBill');
                $CompanyName = $DirectBill->addChild('CompanyName', 'RentingCarz');
                $CompanyName->addAttribute('BillingNumber', 'TCP00926');
                $PaymentAmount = $RentalPaymentPref->addChild('PaymentAmount');
                $Amount = (float)$model->base_real + (float)$model->taxes_real;
                $PaymentAmount->addAttribute('Amount', $Amount);
                $PaymentAmount->addAttribute('CurrencyCode', $model->currency);
                break;
            case EntryRateType::MOVIDA_NAME:
                $VehResRQInfo = $otaRQ->addChild('VehResRQInfo');
                //CoveragePrefs
                $CoveragePrefs = $VehResRQInfo->addChild('CoveragePrefs');
                if (isset($additional_information['CoverageType']) && !empty($additional_information['CoverageType'])) {
                    $CoveragePref = $CoveragePrefs->addChild('CoveragePref');
                    $CoveragePref->addAttribute('Code', $additional_information['CoverageType'] ?? 'BAS');
                }
                //Reference
                $Reference = $VehResRQInfo->addChild('Reference');
                $Reference->addAttribute('ID', '401865');
                break;
            case EntryRateType::LOCALIZA_NAME:
                $countCoveragePref = 2;
                $VehResRQInfo = $otaRQ->addChild('VehResRQInfo');
                $CoveragePrefs = $VehResRQInfo->addChild('CoveragePrefs');
                $CoveragePrefs->addAttribute('xmlns', $OTAConexion->getXmlns());
                $CoveragePref = $CoveragePrefs->addChild('CoveragePref');
                if($model->getDestinationCountry() !== 'EC' && $model->getDestinationCountry() !== 'PY') {
                    $CoveragePref->addAttribute('CoverageType', $additional_information['CoverageType'] ?? '7');
                }
                if($model->getDestinationCountry() === 'BR') {
                    $RentalPaymentPref = $VehResRQInfo->addChild('RentalPaymentPref');
                    $RentalPaymentPref->addAttribute('PaymentType', '2');
                    $RentalPaymentPref->addAttribute('xmlns', $OTAConexion->getXmlns());
                    $Voucher = $RentalPaymentPref->addChild('Voucher');
                    $Voucher->addAttribute('ValueType', '2');
                } else {
                    $RentalPaymentPref = $VehResRQInfo->addChild('RentalPaymentPref');
                    $RentalPaymentPref->addAttribute('PaymentType', '1');
                    $RentalPaymentPref->addAttribute('xmlns', $OTAConexion->getXmlns());
                }
                $Reference = $VehResRQInfo->addChild('Reference');
                $Reference->addAttribute('ID', $additional_information['ID']);
                $Reference->addAttribute('Type', $additional_information['Type']);
                $Reference->addAttribute('xmlns', $OTAConexion->getXmlns());
                $TourInfo = $VehResRQInfo->addChild('TourInfo');
                $TourInfo->addAttribute('TourNumber', 'A');
                break;
            case EntryRateType::UNIDAS_NAME:
                $VehResRQInfo = $otaRQ->addChild('VehResRQInfo');
                $CoveragePrefs = $VehResRQInfo->addChild('CoveragePrefs');
                $CoveragePrefs->addAttribute('xmlns', $OTAConexion->getXmlns());
                $CoveragePref = $CoveragePrefs->addChild('CoveragePref');
//                $CoveragePref->addAttribute('CoverageType', $additional_information['CoverageType']);
                $Reference = $VehResRQInfo->addChild('Reference');
                $Reference->addAttribute('ID', $model->rate_identifier);
                break;
            case EntryRateType::ACE_NAME:
                $VehResRQInfo = $otaRQ->addChild('VehResRQInfo');
                $Reference = $VehResRQInfo->addChild('Reference');
                $Reference->addAttribute('ID', $additional_information['ReferenceID']);
                $Reference->addAttribute('Type', $additional_information['ReferenceType']);
                $RentalPaymentPref = $VehResRQInfo->addChild('RentalPaymentPref');
                $PaymentAmount = $RentalPaymentPref->addChild('PaymentAmount');
                $PaymentAmount->addAttribute('Amount', $additional_information['Amount']);
                $PaymentAmount->addAttribute('CurrencyCode', $additional_information['CurrencyCode']);
                break;
            default:
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
}