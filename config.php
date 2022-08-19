<?php

use yii\web\JsonResponseFormatter;
use yii\rest\UrlRule;
use yii\web\UrlManager;
use yii\web\Response;

return [
    'id' => 'example-rental-company-implementation',
    // the basePath of the application will be the `micro-app` directory
    'basePath' => __DIR__,
    // this is where the application will find all controllers
    'controllerNamespace' => 'micro\controllers',
    // set an alias to enable autoloading of classes from the 'micro' namespace
    'aliases' => [
        '@micro' => __DIR__,
    ],
    'components' => [
        'urlManager' => [
            'class' => UrlManager::class,
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/index',
                [
                    'class' => UrlRule::class,
                    'controller' => 'company',
                    'extraPatterns' => [
                        'GET get-matrix' => 'get-matrix',
                        'POST get-matrix' => 'get-matrix',
                        'OPTIONS get-matrix' => 'options',
                        'POST get-selection' => 'get-selection',
                        'OPTIONS get-selection' => 'options',
                        'POST confirmation' => 'confirmation',
                        'OPTIONS confirmation' => 'options',
                        'POST my-reservation' => 'my-reservation',
                        'OPTIONS my-reservation' => 'options',
                        'POST cancel' => 'cancel',
                        'OPTIONS cancel' => 'options',
                        'POST get-offices' => 'get-offices',
                        'OPTIONS get-offices' => 'options',
                    ]
                ],
            ],
        ],
        'response' => [
            'formatters' => [
                Response::FORMAT_JSON => [
                    'class' => JsonResponseFormatter::class,
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'cookieValidationKey' => 'jwNfQSlesA9GCky4Qp-w2x5fTgELtRZ6',
        ],
    ]
];