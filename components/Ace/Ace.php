<?php

namespace micro\components\Ace;

use micro\components\MultipleConexion;

class Ace
{
    public static function getMatrixResult($rates, $credentials, $getDataModel, $environment)
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
        return MultipleConexion::sendMultipleRequests($urls, $request, $services, MultipleConexion::SERVICE_MATRIX, true, $options);
    }
}