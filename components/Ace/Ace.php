<?php

namespace micro\components\Ace;

use micro\components\MultipleConexion;

class Ace
{
    /**
     * Get Matrix Result
     * @param $rates
     * @param $credentials
     * @param $getDataModel
     * @param $environment
     * @param $debug
     * @return array
     */
    public static function getMatrixResult($rates, $credentials, $getDataModel, $environment, $debug): array
    {
        $request = [];
        $urls = [];
        $options = [];
        $services = [];
        foreach ($rates as $rate) {
            $aceConexion = new AceConexion();
            $aceConexion->setCredentials($credentials['url'], $credentials['id'], $credentials['host'], $environment);
            $request[$rate['rate_type_id']] = $aceConexion->OTA_VehAvailRate($getDataModel, $rates, $rate['rate_type_id']);
            $urls[$rate['rate_type_id']] = $credentials['url'];
            $options[$rate['rate_type_id']] = $aceConexion->getOptions('VehAvailRate');
            $services[$rate['rate_type_id']] = 'VehAvailRate';
        }
        return MultipleConexion::sendMultipleRequests($urls, $request, $services, MultipleConexion::SERVICE_MATRIX, $debug, $options);
    }

    /**
     * Get Selection Result
     * @param $rates
     * @param $credentials
     * @param $getDataModel
     * @param $environment
     * @param $debug
     * @return array
     */
    public static function getSelectionResult($rates, $credentials, $getDataModel, $environment, $debug): array
    {
        $request = [];
        $urls = [];
        $options = [];
        $services = [];
        foreach ($rates as $rate) {
            $aceConexion = new AceConexion();
            $aceConexion->setCredentials($credentials['url'], $credentials['id'], $credentials['host'], $environment);
            $request[$rate['rate_type_id']] = $aceConexion->OTA_VehAvailRate($getDataModel, $rates, $rate['rate_type_id']);
            $urls[$rate['rate_type_id']] = $credentials['url'];
            $options[$rate['rate_type_id']] = $aceConexion->getOptions('VehAvailRate');
            $services[$rate['rate_type_id']] = 'VehAvailRate';
        }
        return MultipleConexion::sendMultipleRequests($urls, $request, $services, MultipleConexion::SERVICE_SELECTION, $debug, $options);
    }

    /**
     * Get Confirmation Result
     * @param $reservation
     * @param $credentials
     * @param $environment
     * @param $debug
     * @return array|object
     */
    public static function getConfirmationResult($reservation, $credentials, $environment, $debug)
    {
        $response = [];
        $aceConexion = new AceConexion();
        $aceConexion->setCredentials($credentials['url'], $credentials['id'], $credentials['host'], $environment);
        $request = $aceConexion->OTA_VehRes($reservation);
        $options['ppdAC'] = $aceConexion->getOptions('VehRes');
        if (!empty($request)) {
            $responses = MultipleConexion::sendMultipleRequests($request['urls'], $request['requests'], $request['services'], MultipleConexion::SERVICE_CONFIRMATION, $debug, $options);
        } else {
            $responses = [];
        }
        foreach ($responses as $resp) {
            $response = $resp;
            break;
        }
        return $response;
    }

    /**
     * Get My Reservation Result
     * @param $lastName
     * @param $confirmationCode
     * @param $credentials
     * @param $environment
     * @param $debug
     * @return array|mixed
     */
    public static function getMyReservationResult($lastName, $confirmationCode, $credentials, $environment, $debug)
    {
        $response = [];
        $aceConexion = new AceConexion();
        $aceConexion->setCredentials($credentials['url'], $credentials['id'], $credentials['host'], $environment);
        $request = $aceConexion->OTA_VehRetRes($lastName, $confirmationCode);
        $options['ppdAC'] = $aceConexion->getOptions('VehRetRes');
        $responses = MultipleConexion::sendMultipleRequests($request['urls'], $request['requests'], $request['services'], '', $debug, $options);
        foreach ($responses as $resp) {
            $response = $resp;
            break;
        }
        return $response;
    }

    /**
     * Get Cancel Result
     * @param $lastName
     * @param $confirmationCode
     * @param $credentials
     * @param $environment
     * @param $debug
     * @return array|mixed
     */
    public static function getCancelResult($lastName, $confirmationCode, $credentials, $environment, $debug)
    {
        $response = [];
        $aceConexion = new AceConexion();
        $aceConexion->setCredentials($credentials['url'], $credentials['id'], $credentials['host'], $environment);
        $request = $aceConexion->OTA_VehCancel($lastName, $confirmationCode);
        $options['ppdAC'] = $aceConexion->getOptions('VehCancel');
        $responses = MultipleConexion::sendMultipleRequests($request['urls'], $request['requests'], $request['services'], '', $debug, $options);
        foreach ($responses as $resp) {
            $response = $resp;
            break;
        }
        if (isset($response->soapBody->OTA_VehCancelRS->Errors->Error)) {
            return ['error' => (string)$response->soapBody->OTA_VehCancelRS->Errors->Error];
        }
        return $response;
    }

    /**
     * Get Offices Result
     * @param $countryCode
     * @param $credentials
     * @param $environment
     * @param $debug
     * @return array|mixed
     */
    public static function getOfficesResult($countryCode, $credentials, $environment, $debug)
    {
        $response = [];
        $aceConexion = new AceConexion();
        $aceConexion->setCredentials($credentials['url'], $credentials['id'], $credentials['host'], $environment);
        $request = $aceConexion->OTA_VehLocSearch();
        $options['ppdAC'] = $aceConexion->getOptions('VehLocSearch');
        $responses = MultipleConexion::sendMultipleRequests($request['urls'], $request['requests'], $request['services'], '', $debug, $options);
        foreach ($responses as $resp) {
            $response = $resp;
            break;
        }
        return $response;
    }
}