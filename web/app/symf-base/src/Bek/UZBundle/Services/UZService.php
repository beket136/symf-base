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
        $res = $this->client->request('GET', 'purchase/station/?term=' . $term);
        return $res->getBody();
    }

    private function validateParams(array $params): bool
    {
//        if (count(array_diff_key(self::PARAMSKEYS, $params)) > 0) {
//            return false;
//        }
        return true;
    }

    function getTrainsInfo($params)
    {

        if (!$this->validateParams($params)) {
            return false;
        }

        $requestParams['form_params'] = $params;
        $res = $this->client->post('purchase/search/', $requestParams);
        return $res->getBody();
    }

    function matchTerm(string $term)
    {
        $stationsList = $this->getStationsByTerm($term);
    }
}
