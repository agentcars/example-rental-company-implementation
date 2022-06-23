<?php

namespace micro\components\Ace;

class AceResponseOffices
{
    /**
     * Process Response
     * @param $response
     * @param $countryCode
     * @param $companyName
     * @param $companyCode
     * @return array
     */
    public static function processResponse($response, $countryCode, $companyName, $companyCode): array
    {
        $offices = [];
        $body = isset($response->soapBody->OTA_VehLocSearchRS->Success) ? $response->soapBody->OTA_VehLocSearchRS : false;
        if ($body) {
            foreach ($body->VehMatchedLocs->VehMatchedLoc as $VehMatchedLoc) {
                $locationDetailAttr = [];
                foreach ($VehMatchedLoc->LocationDetail->attributes() as $attribute => $value) {
                    $locationDetailAttr[$attribute] = (string)$value;
                }
                $telephoneAttr = [];
                foreach ($VehMatchedLoc->LocationDetail->Telephone->attributes() as $attribute => $value) {
                    $telephoneAttr[$attribute] = (string)$value;
                }
                $stateCode = '';
                if (isset($VehMatchedLoc->LocationDetail->Address->StateProv)) {
                    foreach ($VehMatchedLoc->LocationDetail->Address->StateProv->attributes() as $attribute => $value) {
                        if ($attribute == 'StateCode') {
                            $stateCode = (string)$value;
                        }
                    }
                }
                $locationDetailAttr['phone'] = $telephoneAttr['PhoneNumber'];
                $Code = $locationDetailAttr['Code'] ?? null;
                $ExtendedLocationCode = $locationDetailAttr['ExtendedLocationCode'] ?? null;
                if ($Code !== null) {
                    $locationList = [];
                    $shuttle_info = null;
                    $iata = null;
                    //Airport
                    if (isset($locationDetailAttr['AtAirport']) && $locationDetailAttr['AtAirport'] === 'true') {
                        $cityOrAirport = 'T';
                    } else {
                        $cityOrAirport = 'C';
                    }
                    if (!empty($ExtendedLocationCode)) {
                        if ($cityOrAirport === 'T') {
                            $shuttle_info = '1';
                            $iata = $ExtendedLocationCode;
                        }
                    }
                    if (isset($VehMatchedLoc->LocationDetail->Address->AddressLine[1])) {
                        $latLng = (string)$VehMatchedLoc->LocationDetail->Address->AddressLine[1];
                        $latLng = str_replace(['  ', '.-', '. ', ', ', ' '], [',', ',-', ',', ',', ','], $latLng);
                        $latLng = explode(',', $latLng);
                    }
                    //Schedule
                    if (isset($VehMatchedLoc->LocationDetail->AdditionalInfo->OperationSchedules->OperationSchedule->OperationTimes)) {
                        $schedule = self::getOTASchedule($VehMatchedLoc->LocationDetail->AdditionalInfo->OperationSchedules->OperationSchedule->OperationTimes);
                    } else {
                        $schedule = null;
                    }
                    //Create Location List
                    $office_code = null;
                    //Lat y Lng
                    $latLngAce = self::getLatLng($countryCode, $Code);
                    $latLng[0] = $latLngAce['lat'] ?? null;
                    $latLng[1] = $latLngAce['lng'] ?? null;
                    $office_code = $Code;
                    $locationList['office_code'] = $office_code;
                    $locationList['update_office'] = 1;
                    $locationList['company_name'] = $companyName;
                    $locationList['company_code'] = $companyCode;

                    $address = '';
                    if (isset($locationDetailAttr['Name'])) {
                        $address .= $locationDetailAttr['Name'] . ', ';
                    }
                    if (isset($VehMatchedLoc->LocationDetail->Address->AddressLine)) {
                        $address .= $VehMatchedLoc->LocationDetail->Address->AddressLine . ', ';
                    }
                    if (isset($VehMatchedLoc->LocationDetail->Address->StreetNmbr)) {
                        $address .= $VehMatchedLoc->LocationDetail->Address->StreetNmbr . ', ';
                    }
                    if (isset($VehMatchedLoc->LocationDetail->Address->CityName)) {
                        $address .= $VehMatchedLoc->LocationDetail->Address->CityName;
                    }
                    $address = trim($address);
                    $address = trim($address, ',');
                    //Shuttle or Terminal
                    if (isset($locationDetailAttr['AtAirport']) && $locationDetailAttr['AtAirport'] === 'true') {
                        if(isset($VehMatchedLoc->LocationDetail->AdditionalInfo->CounterLocation)) {
                            foreach ($VehMatchedLoc->LocationDetail->AdditionalInfo->CounterLocation->attributes() as $attribute => $value) {
                                if($attribute == 'Location') {
                                    $counterLocation = (string)$value;
                                    $shuttle_info = self::getShuttleOrTerminalByCounterLocation($counterLocation);
                                    $iata = $locationDetailAttr['AssocAirportLocList'] ?? null;
                                }
                            }
                        }
                    }
                    $locationDetailAttr['updateDate'] = date('Y-m-d H:i:s');
                    $locationList['status'] = 1;
                    $locationList['address'] = $address;
                    $locationList['lat'] = $latLng[0] ?? '';
                    $locationList['lng'] = $latLng[1] ?? '';
                    $locationList['zip_code'] = isset($VehMatchedLoc->LocationDetail->Address->PostalCode) ? (string)$VehMatchedLoc->LocationDetail->Address->PostalCode : null;
                    $locationList['city_name'] = isset($VehMatchedLoc->LocationDetail->Address->CityName) ? (string)$VehMatchedLoc->LocationDetail->Address->CityName : null;
                    $locationList['state'] = $stateCode;
                    $locationList['country_code'] = $countryCode;
                    $locationList['franchise_code'] = $Code;
                    $locationList['additional_information'] = $locationDetailAttr;
                    $locationList['schedule'] = $schedule;
                    $locationList['shuttle_info'] = $shuttle_info ?? null;
                    $locationList['iata'] = $iata ?? null;
                    $offices[] = $locationList;
                }
            }
        } else {
            return ['error' => isset($response->soapBody->OTA_VehLocSearchRS->Errors->Error) ? (string)$response->soapBody->OTA_VehLocSearchRS->Errors->Error : 'Not defined'];
        }
        return $offices;
    }

    /**
     * Get Lat y Lng Ace offices from file xlsx
     * @param $countryCode
     * @param $officeCode
     * @return mixed|null
     */
    public static function getLatLng($countryCode, $officeCode)
    {
        $offices['AG']['ANUO01'] = ['lat' => '17.136681', 'lng' => '-61.80009'];
        $offices['AL']['TIAO01'] = ['lat' => '41.358058', 'lng' => '19.734131'];
        $offices['AW']['AUAO01'] = ['lat' => '12.508748', 'lng' => '-70.007715'];
        $offices['BB']['BGIT01'] = ['lat' => '13.075418', 'lng' => '-59.493768'];
        $offices['CA']['YULO02'] = ['lat' => '45.452124', 'lng' => '-73.738609'];
        $offices['CA']['YVRN02'] = ['lat' => '49.284143', 'lng' => '-123.114795'];
        $offices['CA']['YVRS02'] = ['lat' => '49.1756887', 'lng' => '-123.1368524'];
        $offices['CA']['YYZO02'] = ['lat' => '43.696459', 'lng' => '-79.618973'];
        $offices['CA']['YYCO02'] = ['lat' => '51.153849', 'lng' => '-113.982784'];
        $offices['CA']['YWGO01'] = ['lat' => '49.902085', 'lng' => '-97.208574'];
        $offices['CA']['YWGE01'] = ['lat' => '49.881406', 'lng' => '-97.084967'];
        $offices['CA']['YWGN01'] = ['lat' => '49.946643', 'lng' => '-97.233957'];
        $offices['CL']['ZCOT01'] = ['lat' => '-38.9271653', 'lng' => '-72.6484008'];
        $offices['CL']['ZCOC01'] = ['lat' => '-38.7405728', 'lng' => '-72.6014299'];
        $offices['CL']['SCLT01'] = ['lat' => '-33.396837', 'lng' => '-70.793753'];
        $offices['CL']['SCLC01'] = ['lat' => '-33.41192', 'lng' => '-70.565896'];
        $offices['CL']['PMCT01'] = ['lat' => '-41.433911', 'lng' => '-73.096676'];
        $offices['CL']['PMCC01'] = ['lat' => '-41.471617', 'lng' => '-72.935582'];
        $offices['CL']['LSCT01'] = ['lat' => '-29.910258', 'lng' => '-71.236507'];
        $offices['CL']['LSCC01'] = ['lat' => '-29.90078', 'lng' => '-71.244766'];
        $offices['CL']['IQQT01'] = ['lat' => '-20.5394', 'lng' => '-70.178285'];
        $offices['CL']['IQQC01'] = ['lat' => '-20.2167', 'lng' => '-70.142223'];
        $offices['CL']['CPOT01'] = ['lat' => '-27.264324', 'lng' => '-70.776244'];
        $offices['CL']['CPOC01'] = ['lat' => '-27.364683', 'lng' => '-70.342058'];
        $offices['CL']['CCPT01'] = ['lat' => '-36.77686', 'lng' => '-73.059511'];
        $offices['CL']['CCPC01'] = ['lat' => '-36.807982', 'lng' => '-73.051888'];
        $offices['CL']['ANFT01'] = ['lat' => '-23.449', 'lng' => '-70.44079'];
        $offices['CL']['ANFC01'] = ['lat' => '-23.531783', 'lng' => '-70.397106'];
        $offices['CO']['PEIT01'] = ['lat' => '4.805167', 'lng' => '-75.793383'];
        $offices['CO']['MDET01'] = ['lat' => '6.173954', 'lng' => '-75.434851'];
        $offices['CO']['BOGT01'] = ['lat' => '4.688779', 'lng' => '-74.135048'];
        $offices['CR']['SJOC01'] = ['lat' => '9.928069', 'lng' => '-84.092913'];
        $offices['CR']['SJOO01'] = ['lat' => '10.002455', 'lng' => '-84.195887'];
        $offices['CR']['LIRO02'] = ['lat' => '10.596628', 'lng' => '-85.532993'];
        $offices['CW']['CURC02'] = ['lat' => '12.152922', 'lng' => '-68.94943'];
        $offices['CW']['CURO01'] = ['lat' => '12.1529218', 'lng' => '-68.9494295'];
        $offices['CY']['QLIC02'] = ['lat' => '34.69835', 'lng' => '33.045542'];
        $offices['CY']['PFOO02'] = ['lat' => '34.724522', 'lng' => '32.518501'];
        $offices['CY']['LCAO02'] = ['lat' => '34.869627', 'lng' => '33.598694'];
        $offices['CZ']['PRGT01'] = ['lat' => '50.10776', 'lng' => '14.270739'];
        $offices['CZ']['PRGC01'] = ['lat' => '50.092011', 'lng' => '14.427255'];
        $offices['DE']['MUCT01'] = ['lat' => '48.354811', 'lng' => '11.760382'];
        $offices['DO']['STIT01'] = ['lat' => '19.402083', 'lng' => '-70.602093'];
        $offices['DO']['SDQT01'] = ['lat' => '18.430606', 'lng' => '-69.676441'];
        $offices['DO']['SDQC01'] = ['lat' => '18.456639', 'lng' => '-69.913599'];
        $offices['ES']['ZAZX01'] = ['lat' => '41.658769', 'lng' => '-0.911136'];
        $offices['ES']['VLCO01'] = ['lat' => '39.48052', 'lng' => '-0.455371'];
        $offices['ES']['TFSO01'] = ['lat' => '28.054212', 'lng' => '-16.610363'];
        $offices['ES']['TFNO01'] = ['lat' => '28.488943', 'lng' => '-16.344656'];
        $offices['ES']['SVQO01'] = ['lat' => '37.429089', 'lng' => '-5.893364'];
        $offices['ES']['SVQX02'] = ['lat' => '37.400342', 'lng' => '-5.961495'];
        $offices['ES']['SPCO01'] = ['lat' => '28.6362368', 'lng' => '-17.7737278'];
        $offices['ES']['SCQO02'] = ['lat' => '42.901291', 'lng' => '-8.428623'];
        $offices['ES']['RMUO01'] = ['lat' => '37.81938', 'lng' => '-1.075859'];
        $offices['ES']['PMIO01'] = ['lat' => '39.539077', 'lng' => '2.735842'];
        $offices['ES']['PMIC01'] = ['lat' => '39.567875', 'lng' => '2.630143'];
        $offices['ES']['MAHO01'] = ['lat' => '39.890074', 'lng' => '4.23688'];
        $offices['ES']['MADO01'] = ['lat' => '40.449325', 'lng' => '-3.561875'];
        $offices['ES']['MADX02'] = ['lat' => '40.406048', 'lng' => '-3.693739'];
        $offices['ES']['LPAO02'] = ['lat' => '27.937629', 'lng' => '-15.390235'];
        $offices['ES']['IBZO01'] = ['lat' => '38.885391', 'lng' => '1.390813'];
        $offices['ES']['IBZP01'] = ['lat' => '39.890074', 'lng' => '4.23688'];
        $offices['ES']['GRXT01'] = ['lat' => '37.185033', 'lng' => '-3.779983'];
        $offices['ES']['FUEO01'] = ['lat' => '28.429785', 'lng' => '-13.87279'];
        $offices['ES']['BIOO02'] = ['lat' => '43.307027', 'lng' => '-2.891708'];
        $offices['ES']['BCNO01'] = ['lat' => '41.315326', 'lng' => '2.070995'];
        $offices['ES']['BCNX02'] = ['lat' => '41.381789', 'lng' => '2.137609'];
        $offices['ES']['ALCO01'] = ['lat' => '38.291193', 'lng' => '-0.570136'];
        $offices['ES']['AGPO01'] = ['lat' => '36.672445', 'lng' => '-4.474159'];
        $offices['ES']['ACEO01'] = ['lat' => '28.958861', 'lng' => '-13.590502'];
        $offices['FI']['TKUT01'] = ['lat' => '60.510785', 'lng' => '22.275591'];
        $offices['FI']['TKUC01'] = ['lat' => '60.443188', 'lng' => '22.230036'];
        $offices['FI']['RVNT01'] = ['lat' => '66.563104', 'lng' => '25.82789'];
        $offices['FI']['RVNT02'] = ['lat' => '66.563104', 'lng' => '25.82789'];
        $offices['FI']['HELO01'] = ['lat' => '60.321042', 'lng' => '24.950672'];
        $offices['FI']['HELO02'] = ['lat' => '60.321042', 'lng' => '24.950672'];
        $offices['FR']['TLST01'] = ['lat' => '43.630548', 'lng' => '1.37652'];
        $offices['FR']['NCET01'] = ['lat' => '43.659427', 'lng' => '7.208491'];
        $offices['FR']['MRST01'] = ['lat' => '43.439083', 'lng' => '5.211946'];
        $offices['FR']['BODT01'] = ['lat' => '44.830335', 'lng' => '-0.709962'];
        $offices['FR']['BIQT01'] = ['lat' => '43.471863', 'lng' => '-1.530726'];
        $offices['GB']['MANO02'] = ['lat' => '53.355685', 'lng' => '-2.205103'];
        $offices['GB']['LHRO01'] = ['lat' => '51.494409', 'lng' => '-0.455917'];
        $offices['GB']['LGWO01'] = ['lat' => '51.168365', 'lng' => '-0.192053'];
        $offices['GB']['BRSO01'] = ['lat' => '51.388079', 'lng' => '-2.69985'];
        $offices['GE']['TBSO01'] = ['lat' => '41.682693', 'lng' => '44.947207'];
        $offices['GE']['TBSW01'] = ['lat' => '41.714687', 'lng' => '44.791787'];
        $offices['GR']['SKGO02'] = ['lat' => '40.5233188', 'lng' => '22.9912339'];
        $offices['GR']['RHOO03'] = ['lat' => '36.382915', 'lng' => '28.133974'];
        $offices['GR']['HERT02'] = ['lat' => '35.336604', 'lng' => '25.168899'];
        $offices['GR']['CHQT02'] = ['lat' => '35.54125', 'lng' => '24.133784'];
        $offices['GR']['ATHO03'] = ['lat' => '37.8872574', 'lng' => '23.9203491'];
        $offices['HN']['TGUT01'] = ['lat' => '14.060701', 'lng' => '-87.21782'];
        $offices['HN']['SAPT01'] = ['lat' => '15.456462', 'lng' => '-87.927546'];
        $offices['HR']['ZAGT01'] = ['lat' => '45.74075', 'lng' => '16.067436'];
        $offices['HR']['SPUT01'] = ['lat' => '43.538925', 'lng' => '16.297774'];
        $offices['HR']['DBVT01'] = ['lat' => '42.560221', 'lng' => '18.260004'];
        $offices['IS']['KEFO02'] = ['lat' => '63.999312', 'lng' => '-22.62962'];
        $offices['IT']['BGYO01'] = ['lat' => '45.476018', 'lng' => '9.685318'];
        $offices['IT']['CIAO01'] = ['lat' => '41.780196', 'lng' => '12.604782'];
        $offices['IT']['FCOO01'] = ['lat' => '41.773363', 'lng' => '12.248811'];
        $offices['IT']['MXPO01'] = ['lat' => '45.606504', 'lng' => '8.708341'];
        $offices['JM']['MBJT01'] = ['lat' => '18.502269', 'lng' => '-77.916623'];
        $offices['JM']['KINT01'] = ['lat' => '17.937179', 'lng' => '-76.780492'];
        $offices['JO']['AQJS01'] = ['lat' => '31.968425', 'lng' => '35.887242'];
        $offices['JO']['AMMO01'] = ['lat' => '31.742611', 'lng' => '35.941112'];
        $offices['JO']['AMMN01'] = ['lat' => '31.977841', 'lng' => '35.889436'];
        $offices['KE']['MBAO01'] = ['lat' => '-4.036696', 'lng' => '39.595582'];
        $offices['KE']['NBOC01'] = ['lat' => '-1.269691', 'lng' => '36.808361'];
        $offices['KE']['NBOO01'] = ['lat' => '-1.32271', 'lng' => '36.926069'];
        $offices['KE']['WILO01'] = ['lat' => '-1.320061', 'lng' => '36.81495'];
        $offices['KY']['GCMW01'] = ['lat' => '19.316823', 'lng' => '-81.382849'];
        $offices['KY']['GCMT01'] = ['lat' => '19.297474', 'lng' => '-81.356999'];
        $offices['LC']['UVFT01'] = ['lat' => '13.73338', 'lng' => '-60.952478'];
        $offices['LC']['SLUT01'] = ['lat' => '14.02032', 'lng' => '-60.994791'];
        $offices['LT']['VNOT02'] = ['lat' => '54.640594', 'lng' => '25.277778'];
        $offices['LT']['VNOC02'] = ['lat' => '54.674787', 'lng' => '25.285987'];
        $offices['LT']['VNOC01'] = ['lat' => '54.656555', 'lng' => '25.233132'];
        $offices['LT']['PLQC01'] = ['lat' => '55.702892', 'lng' => '21.134613'];
        $offices['LT']['PLQT01'] = ['lat' => '55.973056', 'lng' => '21.093611'];
        $offices['LT']['KUNT01'] = ['lat' => '54.964592', 'lng' => '24.074926'];
        $offices['LV']['RIXC02'] = ['lat' => '56.974512', 'lng' => '24.172892'];
        $offices['LV']['RIXN01'] = ['lat' => '57.144286', 'lng' => '24.856746'];
        $offices['LV']['RIXO01'] = ['lat' => '56.9218786', 'lng' => '23.985903'];
        $offices['MA']['RBAT01'] = ['lat' => '34.03572', 'lng' => '-6.74742'];
        $offices['MA']['RAKT01'] = ['lat' => '31.6011', 'lng' => '-8.025461'];
        $offices['MA']['CMNT01'] = ['lat' => '33.372194', 'lng' => '-7.579676'];
        $offices['MF']['SFGO01'] = ['lat' => '18.101142', 'lng' => '-63.047668'];
        $offices['MT']['MLAO01'] = ['lat' => '35.89374', 'lng' => '14.429852'];
        $offices['MX']['ZIHC01'] = ['lat' => '17.656106', 'lng' => '-101.555119'];
        $offices['MX']['VSAC01'] = ['lat' => '17.998138', 'lng' => '-92.924637'];
        $offices['MX']['TUYC01'] = ['lat' => '20.212007', 'lng' => '-87.452842'];
        $offices['MX']['TRCT01'] = ['lat' => '25.563199', 'lng' => '-103.40111'];
        $offices['MX']['TRCC01'] = ['lat' => '25.549815', 'lng' => '-103.41722'];
        $offices['MX']['TIJT01'] = ['lat' => '32.536163', 'lng' => '-116.980695'];
        $offices['MX']['TGZT01'] = ['lat' => '16.560655', 'lng' => '-93.019242'];
        $offices['MX']['TAMT01'] = ['lat' => '22.282613', 'lng' => '-97.86402'];
        $offices['MX']['SLWC01'] = ['lat' => '25.434851', 'lng' => '-100.979578'];
        $offices['MX']['SLWT01'] = ['lat' => '25.448514', 'lng' => '-101.005739'];
        $offices['MX']['SLPT01'] = ['lat' => '22.256886', 'lng' => '-100.934151'];
        $offices['MX']['SLPC02'] = ['lat' => '22.148035', 'lng' => '-100.947231'];
        $offices['MX']['SJDT03'] = ['lat' => '23.145699', 'lng' => '-109.719736'];
        $offices['MX']['REXC01'] = ['lat' => '22.241529', 'lng' => '-97.856353'];
        $offices['MX']['REXT01'] = ['lat' => '26.011692', 'lng' => '-98.227044'];
        $offices['MX']['QROT01'] = ['lat' => '20.622485', 'lng' => '-100.189454'];
        $offices['MX']['QROC01'] = ['lat' => '20.587872', 'lng' => '-100.418916'];
        $offices['MX']['PVRT01'] = ['lat' => '20.680523', 'lng' => '-105.254564'];
        $offices['MX']['PDST01'] = ['lat' => '28.625119', 'lng' => '-100.537686'];
        $offices['MX']['PCMO01'] = ['lat' => '20.633432', 'lng' => '-87.078125'];
        $offices['MX']['PBCT01'] = ['lat' => '19.163775', 'lng' => '-98.375737'];
        $offices['MX']['PAZT01'] = ['lat' => '20.527059', 'lng' => '-97.462912'];
        $offices['MX']['PAZC01'] = ['lat' => '20.537096', 'lng' => '-97.4716'];
        $offices['MX']['NLDT01'] = ['lat' => '27.541834', 'lng' => '-99.575148'];
        $offices['MX']['NLDC01'] = ['lat' => '22.83283', 'lng' => '-98.437943'];
        $offices['MX']['MEXC01'] = ['lat' => '19.361923', 'lng' => '-99.35048'];
        $offices['MX']['MTYO01'] = ['lat' => '25.779459', 'lng' => '-100.138228'];
        $offices['MX']['MTYC01'] = ['lat' => '25.812778', 'lng' => '-100.151111'];
        $offices['MX']['MIDT01'] = ['lat' => '20.933996', 'lng' => '-89.663029'];
        $offices['MX']['MEXT01'] = ['lat' => '19.436076', 'lng' => '-99.071908'];
        $offices['MX']['MEXC04'] = ['lat' => '19.431745', 'lng' => '-99.157325'];
        $offices['MX']['MAMT01'] = ['lat' => '25.869029', 'lng' => '-97.502738'];
        $offices['MX']['LZCT01'] = ['lat' => '18.001566', 'lng' => '-102.220325'];
        $offices['MX']['LTOT01'] = ['lat' => '25.99212', 'lng' => '-111.352924'];
        $offices['MX']['LAPC01'] = ['lat' => '24.160935', 'lng' => '-110.318325'];
        $offices['MX']['LTOC01'] = ['lat' => '26.01044', 'lng' => '-111.340317'];
        $offices['MX']['LAPT01'] = ['lat' => '24.076617', 'lng' => '-110.36747'];
        $offices['MX']['HMOT01'] = ['lat' => '29.090211', 'lng' => '-111.050638'];
        $offices['MX']['GDLT01'] = ['lat' => '20.527609', 'lng' => '-103.299975'];
        $offices['MX']['GDLC02'] = ['lat' => '20.692371', 'lng' => '-103.375831'];
        $offices['MX']['GDLC01'] = ['lat' => '20.663342', 'lng' => '-103.266402'];
        $offices['MX']['CZMT01'] = ['lat' => '20.511567', 'lng' => '-86.930323'];
        $offices['MX']['CUUT01'] = ['lat' => '28.701604', 'lng' => '-105.964817'];
        $offices['MX']['CUNT01'] = ['lat' => '21.040341', 'lng' => '-86.873564'];
        $offices['MX']['CULT01'] = ['lat' => '24.762985', 'lng' => '-107.476614'];
        $offices['MX']['CUUC01'] = ['lat' => '28.635278', 'lng' => '-106.08888'];
        $offices['MX']['CJST01'] = ['lat' => '31.648325', 'lng' => '-106.483564'];
        $offices['MX']['CJSC01'] = ['lat' => '31.738618', 'lng' => '-106.432734'];
        $offices['MX']['BJXT01'] = ['lat' => '20.993517', 'lng' => '-101.48057'];
        $offices['MX']['MTYT02'] = ['lat' => '25.463841', 'lng' => '-102.402406'];
        $offices['NI']['MGAT01'] = ['lat' => '12.144632', 'lng' => '-86.171329'];
        $offices['NL']['AMSO01'] = ['lat' => '52.32917', 'lng' => '4.78547'];
        $offices['PA']['PTYT01'] = ['lat' => '9.07', 'lng' => '-79.383598'];
        $offices['PA']['PTYC02'] = ['lat' => '8.981003', 'lng' => '-79.522884'];
        $offices['PA']['PACT01'] = ['lat' => '8.973056', 'lng' => '-79.5575'];
        $offices['PA']['ONXC01'] = ['lat' => '8.353226', 'lng' => '-80.148318'];
        $offices['PA']['BLBT01'] = ['lat' => '8.914805', 'lng' => '-79.59812'];
        $offices['PL']['WROT01'] = ['lat' => '51.10941', 'lng' => '16.880965'];
        $offices['PL']['WMIT01'] = ['lat' => '52.449268', 'lng' => '20.649049'];
        $offices['PL']['WAWT01'] = ['lat' => '52.17081', 'lng' => '20.974177'];
        $offices['PL']['POZT01'] = ['lat' => '52.414413', 'lng' => '16.835053'];
        $offices['PL']['KTWO01'] = ['lat' => '50.463267', 'lng' => '19.085301'];
        $offices['PL']['KTWC01'] = ['lat' => '50.235626', 'lng' => '18.976806'];
        $offices['PL']['KRKO01'] = ['lat' => '50.062282', 'lng' => '19.80227'];
        $offices['PT']['PDLO01'] = ['lat' => '37.752745', 'lng' => '-25.643674'];
        $offices['PT']['OPOO02'] = ['lat' => '41.223205', 'lng' => '-8.684285'];
        $offices['PT']['LISO01'] = ['lat' => '38.788222', 'lng' => '-9.120973'];
        $offices['PT']['FAOO01'] = ['lat' => '37.028277', 'lng' => '-7.967707'];
        $offices['RO']['OTPO01'] = ['lat' => '44.573323', 'lng' => '26.069993'];
        $offices['RO']['BUHC01'] = ['lat' => '44.407638', 'lng' => '26.143996'];
        $offices['RS']['BEGT01'] = ['lat' => '44.820372', 'lng' => '20.289973'];
        $offices['SX']['SXMT01'] = ['lat' => '18.042427', 'lng' => '-63.107651'];
        $offices['TC']['PLST01'] = ['lat' => '21.776351', 'lng' => '-72.273523'];
        $offices['TT']['POST01'] = ['lat' => '10.597696', 'lng' => '-61.341716'];
        $offices['US']['EWRO02'] = ['lat' => '40.698304', 'lng' => '-74.189877'];
        $offices['US']['TPAT01'] = ['lat' => '27.961377', 'lng' => '-82.538113'];
        $offices['US']['STXO01'] = ['lat' => '17.7422461', 'lng' => '-64.7124034'];
        $offices['US']['STST01'] = ['lat' => '38.510351', 'lng' => '-122.807836'];
        $offices['US']['STSC01'] = ['lat' => '38.496265', 'lng' => '-122.771898'];
        $offices['US']['SPNT01'] = ['lat' => '15.119743', 'lng' => '145.728279'];
        $offices['US']['SLCO01'] = ['lat' => '40.740823', 'lng' => '-111.958542'];
        $offices['US']['SJUO01'] = ['lat' => '18.442179', 'lng' => '-66.022505'];
        $offices['US']['SJUP01'] = ['lat' => '18.45657', 'lng' => '-66.073452'];
        $offices['US']['SFOO01'] = ['lat' => '37.600855', 'lng' => '-122.373645'];
        $offices['US']['SFOC02'] = ['lat' => '37.600855', 'lng' => '-122.373645'];
        $offices['US']['SANO04'] = ['lat' => '32.727998', 'lng' => '-117.172268'];
        $offices['US']['SANC03'] = ['lat' => '32.727998', 'lng' => '-117.172268'];
        $offices['US']['RALC01'] = ['lat' => '33.991502', 'lng' => '-117.37065'];
        $offices['US']['PSPO01'] = ['lat' => '33.816183', 'lng' => '-116.473325'];
        $offices['US']['PQIT01'] = ['lat' => '46.692128', 'lng' => '-68.045025'];
        $offices['US']['PQIC01'] = ['lat' => '46.691803', 'lng' => '-68.022342'];
        $offices['US']['PNSO01'] = ['lat' => '30.405848', 'lng' => '-87.277444'];
        $offices['US']['PNSC01'] = ['lat' => '30.405848', 'lng' => '-87.277444'];
        $offices['US']['PBIO01'] = ['lat' => '26.693523', 'lng' => '-80.054249'];
        $offices['US']['PBIC01'] = ['lat' => '26.693523', 'lng' => '-80.054249'];
        $offices['US']['ORLC02'] = ['lat' => '28.456099', 'lng' => '-81.270043'];
        $offices['US']['OGGO01'] = ['lat' => '20.886982', 'lng' => '-156.454681'];
        $offices['US']['OGGC01'] = ['lat' => '20.886982', 'lng' => '-156.454681'];
        $offices['US']['NYCC36'] = ['lat' => '40.697309', 'lng' => '-73.956338'];
        $offices['US']['NYCC35'] = ['lat' => '40.620216', 'lng' => '-74.029076'];
        $offices['US']['MSYC01'] = ['lat' => '29.955095', 'lng' => '-90.088985'];
        $offices['US']['MSYO01'] = ['lat' => '29.981735', 'lng' => '-90.256503'];
        $offices['US']['MSPT01'] = ['lat' => '44.865467', 'lng' => '-93.243798'];
        $offices['US']['MSPC01'] = ['lat' => '44.865467', 'lng' => '-93.243798'];
        $offices['US']['MIAT01'] = ['lat' => '25.797107', 'lng' => '-80.262991'];
        $offices['US']['MCOO02'] = ['lat' => '28.456099', 'lng' => '-81.270043'];
        $offices['US']['LGBO01'] = ['lat' => '33.811484', 'lng' => '-118.138451'];
        $offices['US']['LFTO01'] = ['lat' => '30.216279', 'lng' => '-92.006413'];
        $offices['US']['LFTC01'] = ['lat' => '30.134745', 'lng' => '-92.105492'];
        $offices['US']['LCHC01'] = ['lat' => '30.197675', 'lng' => '-93.193924'];
        $offices['US']['LAXO02'] = ['lat' => '33.956737', 'lng' => '-118.381664'];
        $offices['US']['LASO01'] = ['lat' => '36.030941', 'lng' => '-115.174754'];
        $offices['US']['LASC02'] = ['lat' => '36.030941', 'lng' => '-115.174754'];
        $offices['US']['JFKO03'] = ['lat' => '40.663756', 'lng' => '-73.818973'];
        $offices['US']['IAHO01'] = ['lat' => '29.951869', 'lng' => '-95.30803'];
        $offices['US']['HULC01'] = ['lat' => '46.160736', 'lng' => '-67.842666'];
        $offices['US']['HOUO01'] = ['lat' => '29.621723', 'lng' => '-95.223264'];
        $offices['US']['HOUC02'] = ['lat' => '29.621723', 'lng' => '-95.223264'];
        $offices['US']['HOUC01'] = ['lat' => '29.951869', 'lng' => '-95.30803'];
        $offices['US']['HNLO01'] = ['lat' => '21.334109', 'lng' => '-157.907501'];
        $offices['US']['HNLC01'] = ['lat' => '21.334109', 'lng' => '-157.907501'];
        $offices['US']['HFDW01'] = ['lat' => '41.6828', 'lng' => '-72.77709'];
        $offices['US']['HNLE03'] = ['lat' => '21.290155', 'lng' => '-157.841748'];
        $offices['US']['GUMO01'] = ['lat' => '13.489556', 'lng' => '144.784128'];
        $offices['US']['GSPC01'] = ['lat' => '34.84077', 'lng' => '-82.359844'];
        $offices['US']['GPTO01'] = ['lat' => '30.408385', 'lng' => '-89.094607'];
        $offices['US']['FLLT01'] = ['lat' => '26.071451', 'lng' => '-80.141225'];
        $offices['US']['EWRO01'] = ['lat' => '40.686864', 'lng' => '-74.193674'];
        $offices['US']['DFWO01'] = ['lat' => '32.847623', 'lng' => '-97.032515'];
        $offices['US']['DFWC01'] = ['lat' => '32.847623', 'lng' => '-97.032515'];
        $offices['US']['DENO01'] = ['lat' => '39.803877', 'lng' => '-104.689602'];
        $offices['US']['DENC01'] = ['lat' => '39.803877', 'lng' => '-104.689602'];
        $offices['US']['CLTC03'] = ['lat' => '35.229975', 'lng' => '-80.922228'];
        $offices['US']['CLTC02'] = ['lat' => '35.17429', 'lng' => '-80.750771'];
        $offices['US']['CHIC02'] = ['lat' => '41.939058', 'lng' => '-87.88685'];
        $offices['US']['SANC01'] = ['lat' => '32.548312', 'lng' => '-116.974479'];
        $offices['US']['CAEC01'] = ['lat' => '34.049911', 'lng' => '-80.987623'];
        $offices['US']['BZNO01'] = ['lat' => '45.670516', 'lng' => '-111.18762'];
        $offices['US']['BQNO01'] = ['lat' => '18.503471', 'lng' => '-67.136202'];
        $offices['US']['ATLT01'] = ['lat' => '33.644057', 'lng' => '-84.452996'];
        $offices['QA']['DOHO01'] = ['lat' => '25.2839926', 'lng' => '51.4419569'];
        $offices['QA']['DOHC01'] = ['lat' => '25.254732', 'lng' => '51.477639'];
        return $offices[$countryCode][$officeCode] ?? null;
    }

    /**
     * Get OTA Schedule
     * @param $operationTimes
     * @return array
     */
    public static function getOTASchedule($operationTimes): array
    {
        $schedule = [];
        $operationTime = [];
        foreach ($operationTimes->OperationTime as $iValue) {
            $temp = null;
            foreach ($iValue->attributes() as $attribute => $value) {
                $temp[$attribute] = (string)$value;
            }
            $operationTime[] = $temp;
        }
        foreach ($operationTime as $value) {
            if(isset($value['Start'])) {
                $start = explode(':', $value['Start']);
                $hourStart = $start[0] . $start[1];
                $end = explode(':', $value['End']);
                $hourEnd = $end[0] . $end[1];
                if (isset($value['Mon']) && $value['Mon'] === 'true') {
                    $schedule[1][] = [
                        'opening' => $hourStart,
                        'close' => $hourEnd,
                    ];
                }
                if (isset($value['Tue']) && $value['Tue'] === 'true') {
                    $schedule[2][] = [
                        'opening' => $hourStart,
                        'close' => $hourEnd,
                    ];
                }
                if (isset($value['Weds']) && $value['Weds'] === 'true') {
                    $schedule[3][] = [
                        'opening' => $hourStart,
                        'close' => $hourEnd,
                    ];
                }
                if (isset($value['Thur']) && $value['Thur'] === 'true') {
                    $schedule[4][] = [
                        'opening' => $hourStart,
                        'close' => $hourEnd,
                    ];
                }
                if (isset($value['Fri']) && $value['Fri'] === 'true') {
                    $schedule[5][] = [
                        'opening' => $hourStart,
                        'close' => $hourEnd,
                    ];
                }
                if (isset($value['Sat']) && $value['Sat'] === 'true') {
                    $schedule[6][] = [
                        'opening' => $hourStart,
                        'close' => $hourEnd,
                    ];
                }
                if (isset($value['Sun']) && $value['Sun'] === 'true') {
                    $schedule[7][] = [
                        'opening' => $hourStart,
                        'close' => $hourEnd,
                    ];
                }
            }
        }
        return $schedule;
    }

    /**
     * @param $location
     *  1	Terminal
    2	Shuttle on airport
    3	Shuttle off airport
    4	Railway station
    5	Hotel
    6	Car dealer
    7	City center/downtown
    8	East of city center
    9	South of city center
    10	West of city center
    11	North of city center
    12	Port/ferry
    13	Near resort
    14	Airport
    15	Counter in terminal, shuttle to car
    16	Shuttle to counter and car
    17	Counter in terminal, car company shuttle to car off airport grounds
    18	Counter in terminal, airport shuttle to car on airport grounds
    19	Counter in terminal, airport shuttle to car off airport grounds
    20	Car company shuttle to counter, car off airport grounds
    21	Airport shuttle to counter, car on airport grounds
    22	Airport shuttle to counter, car off airport grounds
    23	Multiple shuttles to counter, car on airport grounds
    24	Multiple shuttles to counter, car off airport grounds
    25	Phone for car company pick-up
     * @return string
     */
    public static function getShuttleOrTerminalByCounterLocation($location) {
        //1: 'On Terminal', 2: 'Shuttle', 3: 'Meet & Greet'
        if ($location === '1' || $location === '4' || $location === '5' || $location === '6' || $location === '7' || $location === '8' || $location === '9' || $location === '10' ||
            $location === '11' || $location === '12' || $location === '13' || $location === '14' || $location === '15' || $location === '17' || $location === '18' || $location === '19') {
            return '1';
        }
        if ($location === '2' || $location === '3' || $location === '16' || $location === '20' || $location === '21' || $location === '22' || $location === '23' || $location === '24') {
            return '2';
        }
        return '3';
    }
}