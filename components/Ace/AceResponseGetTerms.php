<?php

namespace micro\components\Ace;

class AceResponseGetTerms
{
    /**
     * Process Response
     * @param $response
     * @return array
     */
    public static function processResponse($response): array
    {
        $termsAndConditions = [];
        $response = $response->soapBody->OTA_VehLocDetailRS;
        //Shuttle Service
        if (isset($response->LocationDetail->AdditionalInfo->Shuttle->ShuttleInfos->ShuttleInfo)) {
            $ShuttleService = [];
            $shuttleInformation = '';
            foreach ($response->LocationDetail->AdditionalInfo->Shuttle->ShuttleInfos->ShuttleInfo as $ShuttleInfo) {
                $value = (string)$ShuttleInfo->SubSection->Paragraph->Text;
                if (!empty($value)) {
                    $ShuttleService[] = $value;
                    $shuttleInformation .= $value . '. ';
                }
            }
            $termsAndConditions['Shuttle Service'] = $ShuttleService;
            $shuttleInformation = !empty($shuttleInformation) ? trim($shuttleInformation) : '';
            //SetShuttleInformation::setShuttleInformationByOfficeAndLanguage('AC', $locationCode, 'en', $shuttleInformation);
        }
        //Policy Payment Options
        if (isset($response->Requirements->PaymentOptions->PaymentOptionsInfo->SubSection->Paragraph->Text)) {
            $PolicyPaymentOptions = [];
            foreach ($response->Requirements->PaymentOptions->PaymentOptionsInfo->SubSection->Paragraph->Text as $Text) {
                $value = (string)$Text;
                if (!empty($value)) {
                    $PolicyPaymentOptions[] = $value;
                }
            }
            $termsAndConditions['Policy Payment Options'] = $PolicyPaymentOptions;
        }
        //RequirementInfo
        if(isset($response->Requirements->RequirementInfos->RequirementInfo)) {
            $i = 0;
            foreach ($response->Requirements->RequirementInfos->RequirementInfo as $RequirementInfo) {
                $RequirementInfoType = '';
                $temp = [];
                foreach ($RequirementInfo->attributes() as $attribute => $value) {
                    if ($attribute == 'Type') {
                        $RequirementInfoType = (string)$value;
                    }
                }
                if (!empty($RequirementInfoType)) {
                    switch ($RequirementInfoType) {
                        case 'Eligibility':
                            $index = 'Policy Renter Qualifications';
                            break;
                        case 'Geographic':
                            $index = 'Policy Geographic';
                            break;
                        case 'Miscellaneous':
                            if ($i === 0) {
                                $index = 'Policy Local Renter';
                            } else {
                                $index = 'Policy Drivers';
                            }
                            $i++;
                            break;
                        default:
                            $index = "Other";
                            break;
                    }
                    foreach ($RequirementInfo->SubSection->Paragraph->Text as $Text) {
                        $value = (string)$Text;
                        if (!empty($value)) {
                            $temp[] = $value;
                        }
                    }
                    $termsAndConditions[$index] = $temp;
                }
            }
        }
        //Return Terms and Conditions
        return $termsAndConditions;
    }
}