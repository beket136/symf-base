<?php
/**
 * Created by PhpStorm.
 * User: sbeke
 * Date: 11/24/17
 * Time: 3:43 PM
 */

namespace Bek\UZBundle\Services;

use GuzzleHttp\Client;


class UZService
{
    const PARAMSKEYS = [
        'station_id_from' => '',
        'station_id_till' => '',
        'station_from' => '',
        'station_till' => '',
        'date_dep' => '',
        'time_dep' => '',
    ];

    /**
     * UZService constructor.
     * @param $baseUrl
     */
    function __construct(string $baseUrl, string $lang)
    {
        $this->client = new \GuzzleHttp\Client(['base_uri' => $baseUrl . $lang . '/']);
    }

    /**
     * @param string $term
     * @return \Psr\Http\Message\StreamInterface
     */
    function getStationsByTerm(string $term)
    {

//        $client = new \GuzzleHttp\Client();
//        $resp = $client->get('https://booking.uz.gov.ua/en/purchase/station/?term=Kyiv');

//        var_dump($resp->getBody()->read(1024));

        $res = $this->client->get( 'purchase/station/?term=' . $term);

        return $res->getBody()->read(1024);
    }

    private function validateParams(array $params): bool
    {
//        if (count(array_diff_key(self::PARAMSKEYS, $params)) > 0) {
//            return false;
//        }

        return true;
    }

    /**
     * @param $params
     * @return bool|\Psr\Http\Message\StreamInterface
     */
    function getTrainsInfo($params)
    {

//        if (!$this->validateParams($params)) {
//            return false;
//        }

        $requestParams['form_params'] = $params;
        $resp = $this->client->post('purchase/search/', $requestParams);
        return $resp->getBody()->read(1024);
    }

    public function buildSearchParams(array $params): array
    {
        $params = self::PARAMSKEYS;
        $params = [
            'station_id_from' => $params['stationIdFrom'],
            'station_id_till' => $params['stationIdTill'],
            'station_from' => '',
            'station_till' => '',
            'date_dep' => '',
            'time_dep' => '',
        ];
//TODO FINISH method implementeation
        return $params;
    }

    /**
     * @param string $term
     */
    function matchTerm(string $term)
    {
        $stationsList = $this->getStationsByTerm($term);
    }
}
