<?php

namespace micro\components\Ace;

final class AceResponseConfirmation
{
    /**
     * Process Response
     * @param $response
     * @return array
     */
    public static function processResponse($response): array
    {
        $result = [];
        if (isset($response->soapBody->OTA_VehResRS->Success)) {
            if (isset($response->soapBody->OTA_VehResRS->VehResRSCore->VehReservation->VehSegmentCore->ConfID)) {
                foreach ($response->soapBody->OTA_VehResRS->VehResRSCore->VehReservation->VehSegmentCore->ConfID->attributes() as $attribute => $value) {
                    if ($attribute == 'ID') {
                        $result['rental_confirmation_code'] = (string)$value;
                    }
                }
                foreach ($response->soapBody->OTA_VehResRS->VehResRSCore->VehReservation->VehSegmentCore->TotalCharge->attributes() as $attribute => $value) {
                    if ($attribute === 'EstimatedTotalAmount') {
                        $result['amount_confirmed'] = (float)$value;
                    }
                    if ($attribute === 'CurrencyCode') {
                        $result['currency_confirmed'] = (string)$value;
                    }
                }
            }
        } else {
            $result['error'] = isset($response->soapBody->OTA_VehResRS->Errors->Error) ? (string)$response->soapBody->OTA_VehResRS->Errors->Error : '';
        }
        return $result;
    }
}