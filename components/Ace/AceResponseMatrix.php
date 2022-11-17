<?php

namespace micro\components\Ace;

final class AceResponseMatrix
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
        foreach ($responses as $response) {
            if (isset($response->soapBody->OTA_VehAvailRateRS->VehAvailRSCore->VehVendorAvails->VehVendorAvail->VehAvails->VehAvail)) {
                foreach ($response->soapBody->OTA_VehAvailRateRS->VehAvailRSCore->VehVendorAvails->VehVendorAvail->VehAvails->VehAvail as $reservationRate) {
                    $isAvailable = true;
                    $coreLogic = [];
                    $coreLogic['companyName'] = $companyName;
                    $coreLogic['companyCode'] = $companyCode;
                    $coreLogic['rateType'] = $getDataModel['rateType'] ?? '';
                    foreach ($reservationRate->VehAvailCore->attributes() as $attribute => $value) {
                        if ($attribute === 'Status' && $value != 'Available') {
                            $isAvailable = false;
                            break;
                        }
                    }
                    if (!$isAvailable) {
                        $result['error'] = 'Not Available';
                        break;
                    }
                    foreach ($reservationRate->VehAvailCore->Vehicle->attributes() as $attribute => $value) {
                        if ($attribute === 'AirConditionInd') {
                            $value = (string)$value;
                            if ($value === 'true') {
                                $coreLogic['air'] = 'Yes';
                            } else {
                                $coreLogic['air'] = 'No';
                            }
                        } else if ($attribute === 'Code') {
                            $value = (string)$value;
                            $coreLogic['sippCode'] = $value;
                        } else if ($attribute === 'PassengerQuantity') {
                            $value = (int)$value;
                            $coreLogic['passengers'] = $value;
                        } else if ($attribute === 'BaggageQuantity') {
                            $value = (int)$value;
                            $coreLogic['bags'] = $value;
                        } else if ($attribute === 'TransmissionType') {
                            $value = (string)$value;
                            $coreLogic['trans'] = $value;
                        }
                    }
                    if (isset($reservationRate->VehAvailCore->Vehicle->VehType)) {
                        foreach ($reservationRate->VehAvailCore->Vehicle->VehType->attributes() as $attribute => $value) {
                            if ($attribute === 'DoorCount') {
                                $doors = substr((string)$value, -1);
                                $coreLogic['doors'] = (int)$doors;
                            }
                        }
                    }

                    $RateQualifierAttr = [];
                    if (isset($reservationRate->VehAvailCore->RentalRate->RateQualifier)) {
                        foreach ($reservationRate->VehAvailCore->RentalRate->RateQualifier->attributes() as $attribute => $value) {
                            $RateQualifierAttr[$attribute] = (string)$value;
                        }
                    }
                    $coreLogic['img'] = 'https://www.acerentacar.com/CarPics/' . $reservationRate->VehAvailCore->Vehicle->PictureURL;
                    $carModelDesc = '';
                    foreach ($reservationRate->VehAvailCore->Vehicle->VehMakeModel->attributes() as $attribute => $value) {
                        if ($attribute === 'Name') {
                            $coreLogic['carModel'] = str_replace(' OU SIMILAR', '', (string)$value);
                        }
                    }
                    $km_included = '';
                    $isUnlimited = false;
                    foreach ($reservationRate->VehAvailCore->RentalRate->RateDistance->attributes() as $attribute => $value) {
                        $value = (string)$value;
                        if ($attribute === 'Unlimited' && $value === 'true') {
                            $km_included = 'Unlimited';
                            $isUnlimited = true;
                        } else if ($attribute === 'Quantity' && !$isUnlimited) {
                            $km_included = $value;
                        } else if ($attribute === 'DistUnitName') {
                            $km_included .= ' ' . ($value === "Mile" ? "Miles" : $value);
                        } else if ($attribute === 'VehiclePeriodUnitName' && !$isUnlimited) {
                            $km_included .= '/' . $value;
                        }
                    }
                    $coreLogic['km_included'] = $km_included;
                    $tax = 0;
                    $taxNotIncluded = 0;
                    $oneway = 0;
                    $RateQualifier = '';
                    foreach ($reservationRate->VehAvailCore->RentalRate->RateQualifier->attributes() as $attribute => $value) {
                        if ($attribute === 'PromotionCode') {
                            $RateQualifier = (string)$value;
                        }
                    }

                    $rates = [];
                    $rate_type_id = [];
                    $rate_type_id[] = 0;
                    if (isset($RateQualifierAttr['RateCategory']) && $RateQualifierAttr['RateCategory'] === '16') {
                        $rate_type_id[] = 1;//JUST_CAR;
                    } else if (isset($RateQualifierAttr['RateCategory']) && $RateQualifierAttr['RateCategory'] === '12') {
                        $rate_type_id[] = 3;//SUPER_PROTECTION;
                        $rate_type_id[] = 2;//BASIC_PROTECTION;
                        $rate_type_id[] = 7;//BASIC_PROTECTION_EU;
                    }
                    foreach ($ratesArr as $j => $w) {
                        $rates[] = $w['code'];
                        if (!isset($payment_option[$w['code']]) || $payment_option[$w['code']] != $w['payment_option']) {
                            if (in_array($w['rate_type_id'], $rate_type_id)) {
                                $coreLogic['rateType'] = (int)$w['rate_type_id'];
                                if ($coreLogic['rateType'] == $w['rate_type_id']) {
                                    $coreLogic['payment_option'] = (int)$w['payment_option'];
                                }
                            }
                        }
                    }
                    if (!in_array($RateQualifier, $rates)) {
                        $result['error'] = 'Not Rate Qualifier in Rates';
                        break;
                    }
                    if (isset($reservationRate->VehAvailCore->Fees->Fee)) {
                        foreach ($reservationRate->VehAvailCore->Fees->Fee as $VehicleCharge) {
                            $IncludedInEstTotalInd = 'false';
                            foreach ($VehicleCharge->attributes() as $attribute => $value) {
                                if ($attribute === 'Amount') {
                                    $Amount = (float)$value;
                                } else if ($attribute === 'IncludedInRate') {
                                    $IncludedInRate = (string)$value;
                                } else if ($attribute === 'Purpose') {
                                    $Purpose = (string)$value;
                                } else if ($attribute === 'IncludedInEstTotalInd') {
                                    $IncludedInEstTotalInd = (string)$value;
                                }
                            }
                            if ($Purpose === '77') {//one way
                                $oneway = $Amount;
                                $coreLogic['oneWayAmount'] = $Amount;
                                if ($IncludedInEstTotalInd === 'true') {
                                    $coreLogic['includeOneWay'] = true;
                                }
                            }
                        }
                    }
                    $TotalChargeAttr = [];
                    foreach ($reservationRate->VehAvailCore->TotalCharge->attributes() as $attribute => $value) {
                        $TotalChargeAttr[$attribute] = (string)$value;
                    }
                    $coreLogic['currency'] = $TotalChargeAttr['CurrencyCode'] ?? '';
                    $coreLogic['realBase'] = (float)$TotalChargeAttr['RateTotalAmount'];
                    $coreLogic['realTax'] = (float)number_format($TotalChargeAttr['EstimatedTotalAmount'] - $TotalChargeAttr['RateTotalAmount'], 2, '.', '');
                    $coreLogic['rateAmount'] = (float)($TotalChargeAttr['EstimatedTotalAmount'] ?? 0);
                    $coreLogic['taxNotIncluded'] = $taxNotIncluded;
                    $coreLogic['pickUpLocation'] = $getDataModel['pickUpLocation'] === 'City' ? $getDataModel['pickUpFranchiseCode'] : $getDataModel['pickUpLocation'];
                    $coreLogic['pickUpFranchiseCode'] = $getDataModel['pickUpFranchiseCode'];
                    //result
                    $result[] = $coreLogic;
                }
            } else if (isset($response->soapBody->OTA_VehAvailRateRS->Errors->Error)) {
                $result['error'] = $response->soapBody->OTA_VehAvailRateRS->Errors->Error;
            }
        }
        return $result;
    }
}