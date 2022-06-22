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
     * @return array
     */
    public static function getMatrixResult($rates, $credentials, $getDataModel, $environment): array
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
        return MultipleConexion::sendMultipleRequests($urls, $request, $services, MultipleConexion::SERVICE_MATRIX, false, $options);
    }

    /**
     * Get Selection Result
     * @param $rates
     * @param $credentials
     * @param $getDataModel
     * @param $environment
     * @return array
     */
    public static function getSelectionResult($rates, $credentials, $getDataModel, $environment): array
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
        return MultipleConexion::sendMultipleRequests($urls, $request, $services, MultipleConexion::SERVICE_MATRIX, false, $options);
    }

    /**
     * Get Confirmation Result
     * @param $reservation
     * @param $credentials
     * @param $environment
     * @return array|object
     */
    public static function getConfirmationResult($reservation, $credentials, $environment)
    {
        $response = [];
        $aceConexion = new AceConexion();
        $aceConexion->setCredentials($credentials['url'], $credentials['id'], $credentials['host'], $environment);
        $request = $aceConexion->OTA_VehRes($reservation);
        $options['ppdAC'] = $aceConexion->getOptions('VehRes');
        if (!empty($request)) {
            $responses = MultipleConexion::sendMultipleRequests($request['urls'], $request['requests'], $request['services'], MultipleConexion::SERVICE_CONFIRMATION, false, $options);
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
     * @return array|mixed
     */
    public static function getMyReservationResult($lastName, $confirmationCode, $credentials, $environment)
    {
        $response = [];
        $aceConexion = new AceConexion();
        $aceConexion->setCredentials($credentials['url'], $credentials['id'], $credentials['host'], $environment);
        $request = $aceConexion->OTA_VehRetRes($lastName, $confirmationCode);
        $options['ppdAC'] = $aceConexion->getOptions('VehRetRes');
        $responses = MultipleConexion::sendMultipleRequests($request['urls'], $request['requests'], $request['services'], false, false, $options);
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
     * @return array|mixed
     */
    public static function getCancelResult($lastName, $confirmationCode, $credentials, $environment)
    {
        $response = [];
        $aceConexion = new AceConexion();
        $aceConexion->setCredentials($credentials['url'], $credentials['id'], $credentials['host'], $environment);
        $request = $aceConexion->OTA_VehCancel($lastName, $confirmationCode);
        $options['ppdAC'] = $aceConexion->getOptions('VehCancel');
        $responses = MultipleConexion::sendMultipleRequests($request['urls'], $request['requests'], $request['services'], false, false, $options);
        foreach ($responses as $resp) {
            $response = $resp;
            break;
        }
        return $response;
    }
}