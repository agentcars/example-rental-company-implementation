<?php

namespace micro\components\OTA;

use common\models\EntryRateType;
use SimpleXMLElement;
use yii\base\Component;

class OTAConexion extends Component
{
    //Attributes
    private $url = '';
    private $Partner = '';
    private $header = '';
    private $EchoToken = '';
    private $user = '';
    private $password = '';
    private $Envelope = '';
    private $Target = '';
    private $namespaceSoap = 'http://schemas.xmlsoap.org/soap/envelope/';
    private $xmlns = 'http://www.opentravel.org/OTA/2003/05';
    public $prefixNameSpace = false;
    private $xsi = '';
    private $version = '';
    private $ID = '';
    private $Type = '';
    private $MessagePassword = '';
    private $companyCode = '';
    private $entryRateType = '';
    private $PickUpLocation = '';
    private $ReturnLocation = '';
    private $ISOCountry = '';
    private $CompanyName = '';
    private $coveragePrefCode = '';
    private $RateCategory = '';
    private $PromotionCode = '';

    /**
     * @param $requestSoap
     * @return mixed
     */
    public function setHeader($requestSoap)
    {
        if (!empty($this->getUser()) && !empty($this->getPassword())) {
            $header = $requestSoap->addChild('Header');
            switch ($this->entryRateType) {
                case EntryRateType::UNIDAS_NAME:
                    $authenticationNode = $header->addChild('Usuario', '', 'http://www.unidas.com.br/');
                    $userNode = 'Acordo';
                    $passwordNode = 'Senha';
                    break;
                default:
                    $authenticationNode = $header->addChild('User');
                    $userNode = 'Agreement';
                    $passwordNode = 'Password';
            }
            $authenticationNode->addChild($userNode, $this->user);
            $authenticationNode->addChild($passwordNode, $this->password);
        }
        return $requestSoap;
    }

    /**
     * Get User
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set User
     *
     * @param $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get User
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set User
     *
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set Namespace SOAP
     * @param $nameSpaceSoap
     */
    public function setNamespaceSoap($nameSpaceSoap)
    {
        $this->namespaceSoap = $nameSpaceSoap;
    }

    /**
     * Get Namespace SOAP
     *
     * @return string
     */
    public function getNamespaceSoap()
    {
        return $this->namespaceSoap;
    }

    /**
     * Set Xmlns
     * @param $xmlns
     */
    public function setXmlns($xmlns)
    {
        $this->xmlns = $xmlns;
    }

    /**
     * Get Xmlns
     *
     * @return string
     */
    public function getXmlns()
    {
        return $this->xmlns;
    }

    /**
     * Get Version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set Version
     *
     * @param $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get Xsi
     *
     * @return string
     */
    public function getXsi()
    {
        return $this->xsi;
    }

    /**
     * Set Xsi
     *
     * @param $xsi
     */
    public function setXsi($xsi)
    {
        $this->xsi = $xsi;
    }

    /**
     * Get ID
     *
     * @return string
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * Set ID
     *
     * @param $ID
     */
    public function setID($ID)
    {
        $this->ID = $ID;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->Type;
    }

    /**
     * Set Type
     *
     * @param $Type
     */
    public function setType($Type)
    {
        $this->Type = $Type;
    }

    /**
     * Get MessagePassword
     *
     * @return string
     */
    public function getMessagePassword()
    {
        return $this->MessagePassword;
    }

    /**
     * Set MessagePassword
     *
     * @param $MessagePassword
     */
    public function setMessagePassword($MessagePassword)
    {
        $this->MessagePassword = $MessagePassword;
    }

    /**
     * Get Company Code
     *
     * @return string
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     * Set Company Code
     *
     * @param $companyCode
     */
    public function setCompanyCode($companyCode)
    {
        $this->companyCode = $companyCode;
    }

    /**
     * Get Entry Rate Type
     *
     * @return string
     */
    public function getEntryRateType()
    {
        return $this->entryRateType;
    }

    /**
     * Set Entry Rate Type
     *
     * @param $entryRateType
     */
    public function setEntryRateType($entryRateType)
    {
        $this->entryRateType = $entryRateType;
    }

    /**
     * Get Pick Up Location
     *
     * @return string
     */
    public function getPickUpLocation()
    {
        return $this->PickUpLocation;
    }

    /**
     * Set Pick Up Location
     *
     * @param $PickUpLocation
     */
    public function setPickUpLocation($PickUpLocation)
    {
        $this->PickUpLocation = $PickUpLocation;
    }

    /**
     * Get Return Location
     *
     * @return string
     */
    public function getReturnLocation()
    {
        return $this->ReturnLocation;
    }

    /**
     * Set Return Location
     *
     * @param $ReturnLocation
     */
    public function setReturnLocation($ReturnLocation)
    {
        $this->ReturnLocation = $ReturnLocation;
    }

    /**
     * Get Echo Token
     *
     * @return string
     */
    public function getEchoToken()
    {
        return $this->EchoToken;
    }

    /**
     * Set Return Location
     *
     * @param $EchoToken
     */
    public function setEchoToken($EchoToken)
    {
        $this->EchoToken = $EchoToken;
    }

    /**
     * Get Target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->Target;
    }

    /**
     * Set Target
     *
     * @param $Target
     */
    public function setTarget($Target)
    {
        $this->Target = $Target;
    }

    /**
     * Get ISO Country
     *
     * @return string
     */
    public function getISOCountry()
    {
        return $this->ISOCountry;
    }

    /**
     * Set ISO Country
     *
     * @param $ISOCountry
     */
    public function setISOCountry($ISOCountry)
    {
        $this->ISOCountry = $ISOCountry;
    }

    /**
     * Get Company Name
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->CompanyName;
    }

    /**
     * Set Company Name
     *
     * @param $CompanyName
     */
    public function setCompanyName($CompanyName)
    {
        $this->CompanyName = $CompanyName;
    }

    /**
     * Get Coverage Pref Code
     *
     * @return string
     */
    public function getCoveragePrefCode()
    {
        return $this->coveragePrefCode;
    }

    /**
     * Set Coverage Pref Code
     *
     * @param $coveragePrefCode
     */
    public function setCoveragePrefCode($coveragePrefCode)
    {
        $this->coveragePrefCode = $coveragePrefCode;
    }

    /**
     * Get Rate Category
     *
     * @return string
     */
    public function getRateCategory() {
        return $this->RateCategory;
    }

    /**
     * Set Rate Category
     *
     * @param $RateCategory
     */
    public function setRateCategory($RateCategory) {
        $this->RateCategory = $RateCategory;
    }

    /**
     * Get Promotion Code
     *
     * @return string
     */
    public function getPromotionCode() {
        return $this->PromotionCode;
    }

    /**
     * Set Promotion Code
     *
     * @param $PromotionCode
     */
    public function setPromotionCode($PromotionCode) {
        $this->PromotionCode = $PromotionCode;
    }

    /**
     * Returns a list of locations based on search filters from input parameters. Each item returned has
     * information about location, such as its name, address, operation schedule, informational messages, etc.
     *
     * @param $cityName
     * @param $countryName
     * @return mixed
     */
    public function OTA_VehLocSearch($cityName, $countryName)
    {
        $object = new ServiceParams\OTA_VehLocSearch();
        return $object->getParameters($cityName, $countryName, $this);
    }

    /**
     * Returns information and details about a location based on its internal code or based on the IATA code
     * of the airport served by the location.
     *
     * @param $locationCode
     * @param string $codeContext
     * @param bool $envelope
     * @return string
     */
    public function OTA_VehLocDetail($locationCode, $codeContext = '', $envelope = true)
    {
        $object = new ServiceParams\OTA_VehLocDetail();
        return $object->getParameters($locationCode, $codeContext, $this, $envelope);
    }

    /**
     * Returns a collection of price quotations for a rental based on rental conditions informed by the client.
     *
     * @param $getDataModel
     * @param $rates
     * @param bool $envelope
     * @return string
     */
    public function OTA_VehAvailRate($getDataModel, $rates, $envelope = true)
    {
        $object = new ServiceParams\OTA_VehAvailRate();
        return $object->getParameters($getDataModel, $rates, $this, $envelope);
    }

    /**
     * Creates a new reservation from data returned by method OTA_VehAvailRate
     *
     * @param $model
     * @param bool $envelope
     * @return string
     */
    public function OTA_VehRes($reservation, $envelope = true)
    {
        $object = new ServiceParams\OTA_VehRes();
        return $object->getParameters($reservation, $this, $envelope);
    }

    /**
     * Returns general information about a reservation that was created through the OTA service
     *
     * @param $lastName
     * @param $confirmationCode
     * @param bool $envelope
     * @return mixed
     */
    public function OTA_VehRetRes($lastName, $confirmationCode, $envelope = true)
    {
        $object = new ServiceParams\OTA_VehRetRes();
        return $object->getParameters($lastName, $confirmationCode, $this, $envelope);
    }

    /**
     * Cancels a reservation created through the OTA service.
     *
     * @param $lastName
     * @param $confirmationCode
     * @param bool $envelope
     * @return mixed
     */
    public function OTA_VehCancel($lastName, $confirmationCode, $envelope = true)
    {
        $object = new ServiceParams\OTA_VehCancel();
        return $object->getParameters($lastName, $confirmationCode, $this, $envelope);
    }

    /**
     * Set Envelope
     *
     * @param $Envelope
     */
    public function setEnvelope($Envelope)
    {
        $this->Envelope = $Envelope;
    }

    /**
     * Get Envelope
     *
     * @return SimpleXMLElement
     */
    public function getEnvelope()
    {
        if (!empty($this->Envelope)) {
            return simplexml_load_string($this->Envelope, 'SimpleXMLElement', 0, $this->namespaceSoap, true);
        }
        if($this->prefixNameSpace) {
            return simplexml_load_string('<soap:Envelope xmlns:soap="' . $this->namespaceSoap . '" xmlns:ns="' . $this->xmlns . '" />', 'SimpleXMLElement', 0, $this->namespaceSoap, true);
        }
        return simplexml_load_string('<soap:Envelope xmlns:soap="' . $this->namespaceSoap . '"  xmlns:ns="' . $this->xmlns . '" />', 'SimpleXMLElement', 0);
    }

    /**
     * Get POS
     *
     * @param $otaRQ
     * @param bool $showXmlns
     */
    public function getPos($otaRQ, $showXmlns = false)
    {
        $POS = $otaRQ->addChild('POS');
        //Source
        $Source = $POS->addChild('Source');
        if (!empty($this->getISOCountry())) {
            $Source->addAttribute('ISOCountry', $this->getISOCountry());
        }
        //CodeContext
        $RequestorID = $Source->addChild('RequestorID');
        if (!empty($this->getID())) {
            $RequestorID->addAttribute('ID', $this->getID());
        }
        if (!empty($this->getType())) {
            $RequestorID->addAttribute('Type', $this->getType());
        }
        if (!empty($this->getMessagePassword())) {
            $RequestorID->addAttribute('MessagePassword', $this->getMessagePassword());
        }
        if($showXmlns){
            $RequestorID->addAttribute('xmlns', $this->getXmlns());
        }
        //CompanyName
        if (!empty($this->getCompanyName())) {
            $CompanyName = $Source->addChild('CompanyName', $this->getCompanyName());
        }
    }

    /**
     * Vehicle Category
     * 1    Car/ sedan
     * 2    Van
     * 3    SUV
     * 4    Convertible
     * 5    Truck
     * 6    Motorcycle
     * 7    Limo
     * 8    Station wagon
     * 9    Pickup
     * 10    Motorhome
     * 11    All-Terrain
     * 12    Recreational
     * 13    Sport
     * 14    Special
     * 15    Extended cab pickup
     * 16    Regular cab pickup
     * 17    Special offer
     * 18    Coupe
     * 19    Monospace
     * 20    2 wheel vehicle
     * 21    Roadster
     * 22    Crossover
     * 23    Commercial van/truck
     * 24    Other
     * 25    Coach
     * @param $category
     * @return int|string
     */
    public function getVehicleCategory($category)
    {
        $category = strtolower($category);
        $van = strpos($category, 'van');
        $suv = strpos($category, 'suv');
        $convertible = strpos($category, 'convertible');
        $stationWagon = strpos($category, 'station wagon');
        $sport = strpos($category, 'sport');
        $special = strpos($category, 'special');

        if ($van !== false) {
            $vehicleCategory = 2;
        } else if ($suv !== false) {
            $vehicleCategory = 3;
        } else if ($convertible !== false) {
            $vehicleCategory = 4;
        } else if ($stationWagon !== false) {
            $vehicleCategory = 8;
        } else if ($sport !== false) {
            $vehicleCategory = 13;
        } else if ($special !== false) {
            $vehicleCategory = 14;
        } else {
            $vehicleCategory = 1;
        }
        return $vehicleCategory;
    }

    /**
     * @param $category
     * @return int|mixed
     */
    public function getVehicleSize($category)
    {
        $vehicleSizes = [
            'Mini' => 1,
            'Subcompact' => 2,
            'Economy' => 3,
            'Compact' => 4,
            'Midsize' => 5,
            'Intermediate' => 6,
            'Standard' => 7,
            'Fullsize' => 8,
            'Luxury' => 9,
            'Premium' => 10,
            'Minivan' => 11,
            '12 passenger van' => 12,
            'Moving van' => 13,
            '15 passenger van' => 14,
            'Cargo van' => 15,
            'not offered' => 16,//16-21
            'Regular' => 22,
            'Unique' => 23,
            'Exotic' => 24,
            'Small/medium truck' => 25,
            'Large truck' => 26,
            'Small SUV' => 27,
            'Medium SUV' => 28,
            'Large SUV' => 29,
            'Exotic SUV' => 30,
            'Four wheel drive' => 31,
            'Special' => 32,
            'Mini elite' => 33,
            'Economy elite' => 34,
            'Compact elite' => 35,
            'Intermediate elite' => 36,
            'Standard elite' => 37,
            'Fullsize elite' => 38,
            'Premium elite' => 39,
            'Luxury elite' => 40,
            'Oversize' => 41,
        ];
        return $vehicleSizes[$category] ?? 21;
    }

}