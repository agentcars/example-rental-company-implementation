<?php

namespace micro\components\AWS;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Yii;
use yii\web\HttpException;

class FileObject
{
    /**
     * Upload Object
     * @param $object
     * @return array|string|void|null
     * @throws HttpException
     */
    public function uploadObject($object)
    {
        $args['version'] = 'latest';
        if (!YII_ENV_PROD) {//uncomment two lines for dev environment in web/index.php
            $args['credentials'] = [
                "key" => $_ENV['S3_KEY'],
                "secret" => $_ENV['S3_SECRET_KEY'],
                "token" => $_ENV['S3_TOKEN'],
                "bucket" => $_ENV['S3_BUCKET']
            ];
        }
        $args['region'] = $_ENV['S3_REGION'] ?? 'us-west-2';
        //Create an S3Client
        $s3client = new S3Client($args);
        $basePath = Yii::$app->basePath;
        $path = '/files/' . $object;
        $bucket_path = $object;
        try {
            $result = $s3client->putObject([
                'Bucket' => $_ENV['S3_BUCKET'] ?? 'rtcrz-0001-rtc-rtcp-01-confirmationxml',
                'Key' => $bucket_path,
                'Body' => fopen($basePath . $path, 'rb+')
            ]);
            return $result['ObjectURL'] ? $bucket_path : ['error' => $result];
        } catch (AwsException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}