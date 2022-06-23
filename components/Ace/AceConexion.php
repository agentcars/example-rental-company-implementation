<?php

namespace micro\components\Ace;

use common\models\RateType;
use http\Exception;
use yii\base\Component;
use yii\helpers\VarDumper;

class AceConexion extends Component
{
    private $url;
    private $Host;
    private $ID;
    private $Type;
    public $request;
    public $response;
    private $namespaceSoap;
    private $xsi;
    private $xmlns;
    private $version;
    private $Target;

    /**
     * init method
     */
    public function init()
    {
        $this->namespaceSoap = 'http://www.w3.org/2003/05/soap-envelope';
        $this->xsi = 'http://www.w3.org/2001/XMLSchema-instance';
        $this->xmlns = 'http://www.opentravel.org/OTA/2003/05';
        $this->version = '5.0';
        $this->Type = '22';
    }

    /**
     * OTA_VehLocSearch
     * @return array
     */
    public function OTA_VehLocSearch()
    {
        $object = new ServiceParams\OTA_VehLocSearch();
        $request['ppdAC'] =  $object->getParameters($this->ID, $this->Type, $this->namespaceSoap, $this->xsi, $this->xmlns, $this->version, $this->Target);
        $url['ppdAC'] = $this->url;
        return $this->getService($object->getServiceName(), $request, $url);
    }

    /**
     * This service gets information about available vehicles from a specified branch at the selected datetime.
     * @param $getDataModel
     * @param $rates
     * @param $index
     * @return array
     */
    public function OTA_VehAvailRate($getDataModel, $rates, $index)
    {
        $object = new ServiceParams\OTA_VehAvailRate();
        $rateCategory = '';
        if ($index == 1) {
            $rateCategory = '16';
        } else if ($index == 3 || $index == 2 || $index == 7) {
            $rateCategory = '12';
        }
        return $object->getParameters($getDataModel, $rates, $this->ID, $this->Type, $this->namespaceSoap, $this->xsi, $this->xmlns, $this->version, $this->Target, $rateCategory);
    }

    /**
     * The OTA_VehResRQ request is used to create a reservation.
     * @param $model
     * @return array
     */
    public function OTA_VehRes($model)
    {
        $object = new ServiceParams\OTA_VehRes();
        $request['ppdAC'] = $object->getParameters($model, $this->ID, $this->Type, $this->namespaceSoap, $this->xsi, $this->xmlns, $this->version, $this->Target);
        $url['ppdAC'] = $this->url;
        return $this->getService($object->getServiceName(), $request, $url);
    }

    /**
     * Returns general information about a reservation that was created through the OTA service.
     * @param $lastName
     * @param $confirmationCode
     * @return array
     */
    public function OTA_VehRetRes($lastName, $confirmationCode)
    {
        $object = new ServiceParams\OTA_VehRetRes();
        $request['ppdAC'] = $object->getParameters($lastName, $confirmationCode, $this->ID, $this->Type, $this->namespaceSoap, $this->xsi, $this->xmlns, $this->version, $this->Target);
        $url['ppdAC'] = $this->url;
        return $this->getService($object->getServiceName(), $request, $url);
    }

    /**
     * Cancels a reservation created through the OTA service.
     * @param $lastName
     * @param $confirmationCode
     * @return array
     */
    public function OTA_VehCancel($lastName, $confirmationCode){
        $object = new ServiceParams\OTA_VehCancel();
        $request['ppdAC'] = $object->getParameters($lastName, $confirmationCode, $this->ID, $this->Type, $this->namespaceSoap, $this->xsi, $this->xmlns, $this->version, $this->Target);
        $url['ppdAC'] = $this->url;
        return $this->getService($object->getServiceName(), $request, $url);
    }

    /**
     * @param $service
     * @param $parameters
     * @param $urls
     * @return array
     */
    public function getService($service, $parameters, $urls)
    {
        $request = [];
        $services = [];
        $response = [];
        try {
            foreach ($parameters as $type => $parameter) {
                if (isset($parameter['error'])) {
                    $response = $parameter;
                    continue;
                } else {
                    $this->request[$type] = $parameter;
                    $request[$type] = $parameter;
                    $services[$type] = $service;
                }
            }
        } catch (\Exception $e) {
            if (!empty($e)) {
                \Yii::error(VarDumper::export($e), 'error_response_Ace');
            }
            return [];
        }
        if (!empty($request) && !empty($services)) {
            $response = [
                'requests' => $request,
                'urls' => $urls,
                'services' => $services,
            ];
        }
        return $response;
    }

    /**
     * Get Options
     * @param $SOAPAction
     * @return string[]
     */
    public function getOptions($SOAPAction)
    {
        return [
            'Content-Type: application/soap+xml;charset=UTF-8;action="'. $SOAPAction .'"',
            'Host: ' . $this->Host
        ];
    }

    /**
     * Set Credentials
     */
    public function setCredentials($url, $id, $host, $target)
    {
        $this->url = $url;
        $this->ID = $id;
        $this->Host = $host;
        $this->Target = $target;
    }
}