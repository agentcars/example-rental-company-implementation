<?php

namespace micro\controllers;

use micro\components\Ace\Ace;
use micro\components\Ace\AceResponseMatrix;
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
        //\Yii::$app->response->format = Response::FORMAT_RAW;
        if (!\Yii::$app->request->post()) {
            throw new HttpException(500, 'Parameters not found');
        }
        $postParams = \Yii::$app->request->post();
        $responses = Ace::getMatrixResult($postParams['rates'], $postParams['credentials'], $postParams['getDataModel'], $postParams['environment']);
        return AceResponseMatrix::processResponse($responses, $postParams['rates'], $postParams['getDataModel']);
    }
}