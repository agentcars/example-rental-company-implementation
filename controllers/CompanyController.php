<?php

namespace micro\controllers;

use micro\components\Ace\Ace;
use micro\components\Ace\AceResponseConfirmation;
use micro\components\Ace\AceResponseGetTerms;
use micro\components\Ace\AceResponseMatrix;
use micro\components\Ace\AceResponseMyReservation;
use micro\components\Ace\AceResponseOffices;
use micro\components\Ace\AceResponseSelection;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\HttpException;
use yii\web\Response;

class CompanyController extends ActiveController
{
    public $enableCsrfValidation = false;
    public $modelClass = '';

    public static function allowedDomains()
    {
        return [
            '*',// star allows all domains
        ];
    }

    public function behaviors()
    {
        // remove rateLimiter which requires an authenticated user to work
        $behaviors = parent::behaviors();
        unset($behaviors['rateLimiter']);
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formatParam' => '_format',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ]
        ];
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => static::allowedDomains(),
                'Access-Control-Request-Method' => ['POST', 'GET', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Max-Age' => 3600,
            ],
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    /**
     * @throws HttpException
     */
    public function actionGetMatrix()
    {
        if (!\Yii::$app->request->post()) {
            throw new HttpException(500, 'Parameters not found');
        }
        $postParams = \Yii::$app->request->post();
        $debug = $postParams['debug'] ?? false;
        $responses = Ace::getMatrixResult($postParams['rates'], $postParams['credentials'], $postParams['getDataModel'], $postParams['environment'], $debug);
        if (empty($responses)) {
            throw new HttpException(500, 'Empty response');
        }
        $result = AceResponseMatrix::processResponse($responses, $postParams['rates'], $postParams['getDataModel'], $postParams['companyName'], $postParams['companyCode']);
        if (isset($result['error'])) {
            throw new HttpException(500, $result['error']);
        }
        return $result;
    }

    /**
     * @throws HttpException
     */
    public function actionGetSelection()
    {
        if (!\Yii::$app->request->post()) {
            throw new HttpException(500, 'Parameters not found');
        }
        $postParams = \Yii::$app->request->post();
        $debug = $postParams['debug'] ?? false;
        $responses = Ace::getSelectionResult($postParams['rates'], $postParams['credentials'], $postParams['getDataModel'], $postParams['environment'], $debug);
        if (empty($responses)) {
            throw new HttpException(500, 'Empty response');
        }
        $result = AceResponseSelection::processResponse($responses, $postParams['rates'], $postParams['getDataModel'], $postParams['companyName'], $postParams['companyCode']);
        if (isset($result['error'])) {
            throw new HttpException(500, $result['error']);
        }
        return $result;
    }

    /**
     * @throws HttpException
     */
    public function actionConfirmation()
    {
        if (!\Yii::$app->request->post()) {
            throw new HttpException(500, 'Parameters not found');
        }
        $postParams = \Yii::$app->request->post();
        $debug = $postParams['debug'] ?? false;
        $response = Ace::getConfirmationResult($postParams['reservation'], $postParams['credentials'], $postParams['environment'], $debug);
        if (empty($response)) {
            throw new HttpException(500, 'Empty response');
        }
        $result = AceResponseConfirmation::processResponse($response, $postParams['reservation']);
        if (isset($result['error'])) {
            throw new HttpException(500, $result['error']);
        }
        return $result;
    }

    /**
     * @throws HttpException
     */
    public function actionMyReservation()
    {
        if (!\Yii::$app->request->post()) {
            throw new HttpException(500, 'Parameters not found');
        }
        $postParams = \Yii::$app->request->post();
        $debug = $postParams['debug'] ?? false;
        $response = Ace::getMyReservationResult($postParams['last_name'], $postParams['rental_confirmation_code'], $postParams['credentials'], $postParams['environment'], $debug);
        if (empty($response)) {
            throw new HttpException(500, 'Empty response');
        }
        if (isset($postParams['raw_response']) && !$postParams['raw_response']) {
            $result = AceResponseMyReservation::processResponse($response);
            if (isset($result['error'])) {
                throw new HttpException(500, $result['error']);
            }
            $response = $result;
        }
        return $response;
    }

    /**
     * @throws HttpException
     */
    public function actionCancel()
    {
        if (!\Yii::$app->request->post()) {
            throw new HttpException(500, 'Parameters not found');
        }
        $postParams = \Yii::$app->request->post();
        $debug = $postParams['debug'] ?? false;
        $response = Ace::getCancelResult($postParams['last_name'], $postParams['rental_confirmation_code'], $postParams['id'], $postParams['credentials'], $postParams['environment'], $debug);
        if (empty($response)) {
            throw new HttpException(500, 'Empty response');
        }
        if (isset($response['error'])) {
            throw new HttpException(500, $response['error']);
        }
        return $response;
    }

    /**
     * @throws HttpException
     */
    public function actionGetOffices()
    {
        if (!\Yii::$app->request->post()) {
            throw new HttpException(500, 'Parameters not found');
        }
        $postParams = \Yii::$app->request->post();
        $debug = $postParams['debug'] ?? false;
        $response = Ace::getOfficesResult($postParams['countryCode'], $postParams['credentials'], $postParams['environment'], $debug);
        if (empty($response)) {
            throw new HttpException(500, 'Empty response');
        }
        $result = AceResponseOffices::processResponse($response, $postParams['countryCode'], $postParams['companyName'], $postParams['companyCode']);
        if (isset($result['error'])) {
            throw new HttpException(500, $result['error']);
        }
        return $result;
    }

    /**
     * @throws HttpException
     */
    public function actionGetTerms()
    {
        if (!\Yii::$app->request->post()) {
            throw new HttpException(500, 'Parameters not found');
        }
        $postParams = \Yii::$app->request->post();
        $debug = $postParams['debug'] ?? false;
        $response = Ace::getTermsResult($postParams['locationCode'], $postParams['credentials'], $postParams['environment'], $debug);
        if (empty($response)) {
            throw new HttpException(500, 'Empty response');
        }
        $result = AceResponseGetTerms::processResponse($response);
        if (isset($result['error'])) {
            throw new HttpException(500, $result['error']);
        }
        return $result;
    }
}