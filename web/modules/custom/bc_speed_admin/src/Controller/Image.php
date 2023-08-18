<?php
namespace Drupal\bc_speed_admin\Controller;

Class Image
{
    private static $key = 'UHOaup5GM7OmvEXBvAUAbp7q4oLTUMbRYpV3m3nCEdaRV4QGGemxqTdOw4w2wLJD';
    private static $url = 'https://api.speedadmin.dk/';

    public static function getRequest($id=null, $contentType) {

        $response = null;

        if (!empty($id)) {

            $curl = curl_init(self::$url . '/v1/blobs/' . $id);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                'Content-Type: ' . $contentType ?? 'application/octet-stream',
                'Authorization: ' . self::$key
            );

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($curl, CURLOPT_TIMEOUT, 3);

            $response = curl_exec($curl);


        }

        return $response;

    }

}

