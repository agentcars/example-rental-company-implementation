<?php

namespace micro\components\Ace;

final class AceResponseSelection
{
    /**
     * Process Response
     * @param $responses
     * @param $ratesArr
     * @param $getDataModel
     * @param $companyName
     * @param $companyCode
     * @return array
     */
    public static function processResponse($responses, $ratesArr, $getDataModel, $companyName, $companyCode): array
    {
        $result = [];
        $coreLogic = [];
        $coreLogic['companyName'] = $companyName;
        $coreLogic['companyCode'] = $companyCode;
        $coreLogic['rateType'] = $getDataModel['rateType'] ?? '';
        $coreLogic['getDataModel'] = $getDataModel;
        /*if ($getDataModel['companyCode'] == 'WH') {
            $coreLogic['companyCode'] = base64_decode($getDataModel['ccrc']);
        }*/
        $coreLogic['sippCode'] = $getDataModel['sippCode'];
        foreach ($responses as $codeResp => $response) {
            $rate_type_id = $codeResp;
            $payment_option = [];
            if (isset($response->soapBody->OTA_VehAvailRateRS->Errors->Error)) {
                $result['error'] = $response->soapBody->OTA_VehAvailRateRS->Errors->Error;
                break;
            }
            if(!isset($response->soapBody->OTA_VehAvailRateRS->VehAvailRSCore->VehVendorAvails->VehVendorAvail->VehAvails->VehAvail)) {
                $result['error'] = 'Not VehAvail';
                break;
            }
            $reservationRate = $response->soapBody->OTA_VehAvailRateRS->VehAvailRSCore->VehVendorAvails->VehVendorAvail->VehAvails->VehAvail;
            $Status = '';
            foreach ($reservationRate->VehAvailCore->attributes() as $attribute => $value) {
                if ($attribute == 'Status') {
                    $Status = (string)$value;
                }
            }
            if (!isset($reservationRate->VehAvailCore->RentalRate) || $Status !== 'Available') {
                $result['error'] = 'Not Available';
                break;
            }
            //Vehicle
            $VehicleAttr = [];
            foreach ($reservationRate->VehAvailCore->Vehicle->attributes() as $attribute => $value) {
                $VehicleAttr[$attribute] = (string)$value;
            }
            //TotalCharge
            $TotalChargeAttr = [];
            foreach ($reservationRate->VehAvailCore->TotalCharge->attributes() as $attribute => $value) {
                $TotalChargeAttr[$attribute] = (string)$value;
            }
            //Reference
            $Reference = [];
            foreach ($reservationRate->VehAvailCore->Reference->attributes() as $attribute => $value) {
                $Reference[$attribute] = (string)$value;
            }
            //RateQualifier
            $RateQualifierAttr = [];
            foreach ($reservationRate->VehAvailCore->RentalRate->RateQualifier->attributes() as $attribute => $value) {
                $RateQualifierAttr[$attribute] = (string)$value;
            }
            $coreLogic['rateIdentifier'] = $RateQualifierAttr['RateAuthorizationCode'] ?? '';
            //Shuttle or Terminal
            $shuttleInfo = $parameters['shuttleOrTerminal'][$companyCode] ?? '';
            //TAX
            $tax = 0;
            $taxNotIncluded = 0;
            $basicDetail = [];
            $oneway = 0;
            foreach ($reservationRate->VehAvailCore->Fees->Fee as $Fee) {
                $IncludedInEstTotalInd = 'false';
                foreach ($Fee->attributes() as $attribute => $value) {
                    if ($attribute === 'Amount') {
                        $Amount = (float)$value;
                    } else if ($attribute === 'IncludedInRate') {
                        $IncludedInRate = (string)$value;
                    } else if ($attribute === 'Description') {
                        $Description = (string)$value;
                    } else if ($attribute === 'Purpose') {
                        $Purpose = (string)$value;
                    } else if ($attribute === 'IncludedInEstTotalInd') {
                        $IncludedInEstTotalInd = (string)$value;
                    }
                }
                $basicDetail[] = [
                    'amount' => $Amount,
                    'comment' => $Description,
                ];
                if($Purpose === '2') {//one way
                    $oneway = $Amount;
                    $coreLogic['oneWayAmount'] = $Amount;
                    if ($IncludedInEstTotalInd === 'true') {
                        $coreLogic['includeOneWay'] = true;
                    }
                }
            }
            $coreLogic['img'] = 'https://www.acerentacar.com/CarPics/' . $reservationRate->VehAvailCore->Vehicle->PictureURL;
            $coreLogic['category'] = isset($sippCodesWithCompanies[$companyCode][$coreLogic['sippCode']]) ? $sippCodesWithCompanies[$companyCode][$coreLogic['sippCode']]['categoryName'] : 'Others';
            //Car Info
            foreach ($reservationRate->VehAvailCore->Vehicle->VehMakeModel->attributes() as $attribute => $value) {
                if ($attribute === 'Name') {
                    $coreLogic['carModel'] = str_replace(' OU SIMILAR', '', (string)$value);
                }
            }
            foreach ($reservationRate->VehAvailCore->Vehicle->VehType->attributes() as $attribute => $value) {
                if ($attribute === 'DoorCount') {
                    $doors = substr((string)$value, -1);
                    $coreLogic['doors'] = (int)$doors;
                }
            }
            $coreLogic['passengers'] = (int)($VehicleAttr['PassengerQuantity'] ?? 0);
            $coreLogic['bags'] = (int)($VehicleAttr['BaggageQuantity'] ?? 0);
            $coreLogic['trans'] = $VehicleAttr['TransmissionType'] ?? '';
            $coreLogic['air'] = isset($VehicleAttr['AirConditionInd']) && $VehicleAttr['AirConditionInd'] === 'true' ? 'Yes' : 'No';

            $RateQualifierAttr = [];
            foreach ($reservationRate->VehAvailCore->RentalRate->RateQualifier->attributes() as $attribute => $value) {
                $RateQualifierAttr[$attribute] = (string)$value;
            }

            $coreLogic['rateIdentifier'] = $RateQualifierAttr['PromotionCode'] ?? '';
            $coreLogic['rateType'] = $rate_type_id;
            foreach ($ratesArr as $j => $w) {
                if (!isset($payment_option[$w['code']]) || $payment_option[$w['code']] != $w['payment_option']) {
                    if ($coreLogic['rateType'] == $w['rate_type_id']) {
                        $coreLogic['payment_option'] = (int)$w['payment_option'];
                        $payment_option[$w['code']] = $coreLogic['payment_option'];
                        break;
                    }
                }
            }
            $km_included = '';
            $DistUnitName = 'Km';
            $isUnlimited = false;
            foreach ($reservationRate->VehAvailCore->RentalRate->RateDistance->attributes() as $attribute => $value) {
                $value = (string)$value;
                if ($attribute === 'Unlimited' && $value === 'true') {
                    $km_included = 'Unlimited';
                    $isUnlimited = true;
                } else if ($attribute === 'Quantity' && !$isUnlimited) {
                    $km_included = $value;
                } else if ($attribute === 'DistUnitName') {
                    $DistUnitName = $value;
                    $km_included .= ' ' . $value;
                } else if ($attribute === 'VehiclePeriodUnitName' && !$isUnlimited) {
                    $km_included .= '/' . $value;
                }
            }
            if ($isUnlimited) {
                $coreLogic['km_included'] = $DistUnitName;
            } else {
                $coreLogic['km_included'] = $km_included;
            }
            $coreLogic['currency'] = $TotalChargeAttr['CurrencyCode'] ?? 'USD';
            $coreLogic['realBase'] = (float)$TotalChargeAttr['RateTotalAmount'];
            $coreLogic['realTax'] = (float)number_format($TotalChargeAttr['EstimatedTotalAmount'] - $TotalChargeAttr['RateTotalAmount'], 2, '.', '');
            $coreLogic['rateAmount'] = (float)($TotalChargeAttr['EstimatedTotalAmount'] ?? 0);
            $coreLogic['taxNotIncluded'] = $taxNotIncluded;
            $coreLogic['carInfo'] = [
                $coreLogic['sippCode'] => [
                    'img' => $coreLogic['img'],
                    'carModel' => $coreLogic['carModel'],
                    'sippCode' => $coreLogic['sippCode'],
                    'km_included' => $coreLogic['km_included'],
                    'companyName' => $coreLogic['companyName'],
                    'doors' => $coreLogic['doors'],
                    'passengers' => $coreLogic['passengers'],
                    'bags' => $coreLogic['bags'],
                    'air_conditioner' => $coreLogic['air'],
                    'transmission' => $coreLogic['trans'],
                    'categoryName' => $coreLogic['category'],
                    'companyCode' => $coreLogic['companyCode'],
                    'shuttleInfo' => !empty($shuttleInfo) ? $shuttleInfo : '',
                ]
            ];
            $coreLogic['auxAddRateInformation'] = true;

            //CD code
            $discountCode = '';
            foreach ($ratesArr as $value) {
                foreach ($value['discountCodes'] as $code) {
                    $discountCode = $code['code'];
                }
            }
            if (!empty($discountCode)) {
                $coreLogic['auxAmadeusNumbers']['NumberCD'] = $discountCode;
            }
            $result[$codeResp] = $coreLogic;
        }
        return $result;
    }
}