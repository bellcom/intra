<?php
namespace Drupal\bc_speed_admin\Controller;

Class Request
{
//    private static $key = 'UHOaup5GM7OmvEXBvAUAbp7q4oLTUMbRYpV3m3nCEdaRV4QGGemxqTdOw4w2wLJD';
    private static $url = 'https://api.speedadmin.dk/';

    public static function getRequest($url=null, $arg=null) {

        $response = null;
        $key = \Drupal::config('bc_speed_admin.settings')->get('apikey');
        if (!empty($url) && !empty($key)) {

            $curl = curl_init(self::$url . $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                'Content-Type: application/json',
                'Authorization: ' . $key
            );

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($curl, CURLOPT_TIMEOUT, 3);

            $response = curl_exec($curl);
            $json = json_decode($response);

            if (json_last_error() == 0) $response = $json;

        }

        return $response;

    }

}
