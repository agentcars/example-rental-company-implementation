<?php

namespace micro\components\Ace;

final class AceResponseMyReservation
{
    /**
     * Process Response
     * @param $response
     * @return array
     */
    public static function processResponse($response): array
    {
        $result = [];
        if (isset($response->soapBody->OTA_VehRetResRS->Success)) {
            if (isset($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->VehSegmentCore->ConfID)) {
                foreach ($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->VehSegmentCore->ConfID->attributes() as $attribute => $value) {
                    if ($attribute == 'ID') {
                        $result['rental_confirmation_code'] = (string)$value;
                    }
                }
                foreach ($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->VehSegmentCore->TotalCharge->attributes() as $attribute => $value) {
                    if ($attribute === 'EstimatedTotalAmount') {
                        $result['amount_confirmed'] = (float)$value;
                    }
                    if ($attribute === 'CurrencyCode') {
                        $result['currency_confirmed'] = (string)$value;
                    }
                }
                $result['status'] = 'Active';
                if (isset($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->Customer->Primary->PersonName->GivenName)) {
                    $result['first_name'] = (string)$response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->Customer->Primary->PersonName->GivenName;
                }
                if (isset($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->Customer->Primary->PersonName->Surname)) {
                    $result['last_name'] = (string)$response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->Customer->Primary->PersonName->Surname;
                }
                if (isset($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->Customer->Primary->Email)) {
                    $result['email'] = (string)$response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->Customer->Primary->Email;
                }
                if (isset($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->VehSegmentCore->VehRentalCore->PickUpLocation)) {
                    foreach ($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->VehSegmentCore->VehRentalCore->PickUpLocation->attributes() as $attribute => $value) {
                        if ($attribute == 'LocationCode') {
                            $result['location_pickup'] = (string)$value;
                        }
                    }
                }
                if (isset($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->VehSegmentCore->VehRentalCore->ReturnLocation)) {
                    foreach ($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->VehSegmentCore->VehRentalCore->ReturnLocation->attributes() as $attribute => $value) {
                        if ($attribute == 'LocationCode') {
                            $result['location_dropoff'] = (string)$value;
                        }
                    }
                }
                if (isset($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->VehSegmentCore->VehRentalCore)) {
                    foreach ($response->soapBody->OTA_VehRetResRS->VehRetResRSCore->VehReservation->VehSegmentCore->VehRentalCore->attributes() as $attribute => $value) {
                        if ($attribute == 'PickUpDateTime') {
                            $PickUpDateTime = (string)$value;
                            $arrPickUpDateTime = explode('T', $PickUpDateTime);
                            $result['pickup_date'] = $arrPickUpDateTime[0];
                            if (isset($arrPickUpDateTime[1])) {
                                $result['pickup_hour'] = substr(str_replace(':', '', $arrPickUpDateTime[1]), 0, 4);
                            }
                        } else if ($attribute == 'ReturnDateTime') {
                            $ReturnDateTime = (string)$value;
                            $arrReturnDateTime = explode('T', $ReturnDateTime);
                            $result['dropoff_date'] = $arrReturnDateTime[0];
                            if (isset($arrReturnDateTime[1])) {
                                $result['dropoff_hour'] = substr(str_replace(':', '', $arrReturnDateTime[1]), 0, 4);
                            }
                        }
                    }
                }
            }
        } else {
            $result['error'] = isset($response->soapBody->OTA_VehRetResRS->Errors->Error) ? (string)$response->soapBody->OTA_VehRetResRS->Errors->Error : '';
        }
        return $result;
    }
}