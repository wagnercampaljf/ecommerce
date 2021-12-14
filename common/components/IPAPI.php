<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 07/10/2015
 * Time: 16:37
 */

namespace common\components;

use common\models\Cidade;

class IPAPI
{
    static $fields = 57368;//refer to http://ip-api.com/docs/api:returned_values#field_generator
    static $api = "http://ip-api.com/php/";

    public $status, $regionName, $city, $query, $message, $cidade;

    public static function query($q = null)
    {
        $data = self::communicate($q);
        $result = new static;
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $result->$key = $val;
            }
            $result->cidade = Cidade::find()->joinWith('banners')->byNome($result->city)->one();
        }

        return $result->cidade ? $result->cidade->id : null;
    }

    private function communicate($q)
    {
        if (is_callable('curl_init')) {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, self::$api . $q . '?fields=' . self::$fields);
            curl_setopt($c, CURLOPT_HEADER, false);
            curl_setopt($c, CURLOPT_TIMEOUT, 30);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
            $result_array = unserialize(curl_exec($c));
            curl_close($c);
        } else {
            $result_array = unserialize(file_get_contents(self::$api . $q . '?fields=' . self::$fields));
        }

        return $result_array;
    }
}