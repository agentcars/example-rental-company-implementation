<?php

namespace micro\components;

use micro\components\AWS\FileObject;
use Yii;
use yii\helpers\VarDumper;
use yii\web\HttpException;

final class ReservationSaveXML
{
    public const PATH = '/opt/s3-drive/confirmationsxml/';

    /**
     * Save Confirmation XML
     * @param $request
     * @param $response
     * @param $reservationId
     * @return void
     * @throws HttpException
     */
    public static function saveConfirmationXML($request, $response, $reservationId): void
    {
        $reservationID = $reservationId;
        $path = self::getPath();

        $request = str_replace('XML-Request=', '<?xml version="1.0"?>', $request);//por EP/KD
        try {
            file_put_contents($path . "$reservationID-Confirmation-Request.xml", print_r($request, true));
            file_put_contents($path . "$reservationID-Confirmation-Response.xml", print_r($response, true));
            $file = new FileObject();
            //Guardado en el bucket S3 de xml
            $file->uploadObject( $reservationID."-Confirmation-Request.xml");
            $file->uploadObject( $reservationID."-Confirmation-Response.xml");
        } catch (\Exception $exception) {
            throw new HttpException(500, $exception->getMessage());
        }
    }

    /**
     * Save Cancel XML
     * @param $request
     * @param $response
     * @param $reservationId
     * @return void
     * @throws HttpException
     */
    public static function saveCancelXML($request, $response, $reservationId): void
    {
        $reservationID = $reservationId;
        $path = self::getPath();

        $request = str_replace('XML-Request=', '<?xml version="1.0"?>', $request);//por EP/KD
        try {
            file_put_contents($path . "$reservationID-Cancel-Request.xml", print_r($request, true));
            file_put_contents($path . "$reservationID-Cancel-Response.xml", print_r($response, true));
            $file = new FileObject();
            //Guardado en el bucket S3 de xml
            $file->uploadObject($reservationID."-Cancel-Request.xml");
            $file->uploadObject($reservationID."-Cancel-Response.xml");
        } catch (\Exception $exception) {
            throw new HttpException(500, $exception->getMessage());
        }
    }

    /**
     * Get Path
     * @return string
     */
    public static function getPath()
    {
        return Yii::$app->basePath . '/files/';
    }
}