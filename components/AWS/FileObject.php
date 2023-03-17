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
        $credentials = [
            "key" => $_ENV['S3_KEY'],
            "secret" => $_ENV['S3_SECRET_KEY'],
            "bucket" => $_ENV['S3_BUCKET']
        ];
        $region = $_ENV['S3_REGION'];
        //Create an S3Client
        $s3client = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => $credentials
        ]);
        $basePath = Yii::$app->basePath;
        $path = '/files/' . $object;
        $bucket_path = $object;
        try {
            $result = $s3client->putObject([
                'Bucket' => $_ENV['S3_BUCKET'],
                'Key' => $bucket_path,
                'Body' => fopen($basePath . $path, 'rb+')
            ]);
            return $result['ObjectURL'] ? $bucket_path : ['error' => $result];
        } catch (AwsException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}