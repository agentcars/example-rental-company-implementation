<?php


namespace micro\components;

use Exception;
use SimpleXMLElement;
use Yii;
use yii\base\Component;

class MultipleConexion extends Component
{
    public const SERVICE_MATRIX = 'matrix';
    public const SERVICE_SELECTION = 'selection';
    public const SERVICE_CONFIRMATION = 'confirmation';

    /**
     * Sends multiples request
     * @param $urls
     * @param $requests
     * @param $services
     * @param string $service
     * @param bool $debug
     * @param array $options
     * @param bool $secondIntent
     * @return array
     */
    public static function sendMultipleRequests($urls, $requests, $services, $service = self::SERVICE_SELECTION, $debug = false, $options = [], $secondIntent = false)
    {
        $result = [];
        $curly = [];
        $responses = [];
        $pm = curl_multi_init();
        foreach ($urls as $id => $d) {
            if (!isset($requests[$id])) {
                continue;
            }

            $curly[$id] = curl_init();
            if (is_array($d) && !empty($d['url'])) {
                $url = $d['url'];
            } else {
                $url = $d;
            }
            if (isset($options[$id], $options[$id]['httpHeader'])) {
                curl_setopt_array($curly[$id], array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => YII_ENV_DEV ? 60 : 14,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => $options[$id]['customRequest'] ?: 'GET',
                    CURLOPT_POSTFIELDS => $requests[$id],
                    CURLOPT_HTTPHEADER => $options[$id]['httpHeader'],
                ));
            } else if (isset($options[$id])) {
                curl_setopt_array($curly[$id], array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => YII_ENV_DEV ? 40 : 14,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $requests[$id],
                    CURLOPT_HTTPHEADER => $options[$id],
                ));
            }

            curl_multi_add_handle($pm, $curly[$id]);
        }
        $running = null;
        do {
            curl_multi_exec($pm, $running);
            curl_multi_select($pm);
        } while ($running > 0);
        $emptyResponse = '';
        foreach ($curly as $id => $c) {
            try {
                ini_set('memory_limit', '320M');
                $var = [];
                if ($service === self::SERVICE_MATRIX) {
                    $var[$id] = simplexml_load_string(preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', curl_multi_getcontent($c)), 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_NOENT | LIBXML_NOCDATA | LIBXML_NOBLANKS);

                    if (!isset($var[$id]->soapBody->soapFault) && !isset($var[$id]->Body->Fault)) {
                        /**
                         * Se envia aca, ya que al asignar a una variable todas las respuestas y luego procesar, colapsaba por memoria,
                         * al estar aca, cada respuesta se procesa y el resultado es de mucho menor tamano que lo anterior
                         */
                        $result[$id] = $var[$id];
                    }
                } else {
                    $result[$id] = $var[$id] = simplexml_load_string(preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', curl_multi_getcontent($c)), 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_NOENT | LIBXML_NOCDATA | LIBXML_NOBLANKS);
                    $responses[$id] = curl_multi_getcontent($c);
                    //if empty response
                    if (empty($result[$id])) {
                        if (empty($responses[$id])) {
                            Yii::error('' . ' ' . $id, 'empty_response_multiple_conexion');
                        } else {
                            $result[$id] = $var[$id] = $responses[$id];
                        }
                    }
                }

                if ($debug) {
                    $responses[$id] = curl_multi_getcontent($c);
                }
                //if empty response
                if (empty($var[$id])) {
                    $emptyResponse .= $id . '-';
                }
                $var = null;
            } catch (Exception $e) {
                self::printResponse($requests, $responses, $services);
                if ($service !== self::SERVICE_MATRIX) {
                    $result[$id] = ['error' => $e->getMessage()];
                }
            }
            curl_multi_remove_handle($pm, $c);
        }

        curl_multi_close($pm);
        //Print XMLs
        if ($debug) {
            self::printResponse($requests, $responses, $services);
        }
        $curly = null; //clean memory dont delete
        $c = null; //clean memory dont delete
        $pm = null; //clean memory dont delete
        $responses = null; //clean memory dont delete
        return $result;
    }

    /**
     * Generate XMLs files for debug
     * @param $requests
     * @param $responses
     * @param $services
     */
    public static function printResponse($requests, $responses, $services): void
    {
        $file = (Yii::$app->basePath . '/files/');
        foreach ($requests as $type => $request) {
            if (isset($responses[$type])) {
                $service = $services[$type];
                $request = str_replace('XML-Request=', '<?xml version="1.0"?>', $request);//por EP/KD
                file_put_contents($file . "Request-$type-$service.xml", print_r($request, true));
                file_put_contents($file . "Response-$type-$service.xml", print_r($responses[$type], true));
            }
        }
    }
}
