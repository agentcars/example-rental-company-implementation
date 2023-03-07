<?php

namespace micro\components\AWS;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Yii;
use yii\helpers\VarDumper;
use yii\web\HttpException;

class FileObject
{
    public const BUCKET_REGION = 'us-west-2';
    public const BUCKET_XML = 'confirmationsxml';
    private const CREDENTIALS = [
        'credentials' => [
            "key" => "AKIAZNSJHO2KVD5AHEN6",
            "secret" => "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",
            "bucket" => "rentingtestsdk"],
        "region" => "us-east-1",
    ];

    /**
     * Upload Object
     * @param $object
     * @return array|string|void|null
     * @throws HttpException
     */
    public function uploadObject($object)
    {
        $params = self::CREDENTIALS;
        $credentials = $params['credentials'];
        $region = self::BUCKET_REGION;
        //Create an S3Client
        $s3client = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => $credentials
        ]);
        $basePath = Yii::$app->basePath;
        $path = '/files/' . $object;
        $bucket_path = 'archivos/' . $object;
        try {
            $result = $s3client->putObject([
                'Bucket' => self::BUCKET_XML,
                'Key' => $bucket_path,
                'Body' => fopen($basePath . $path, 'rb+')
            ]);
            return $result['ObjectURL'] ? $bucket_path : ['error' => $result];
        } catch (AwsException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}