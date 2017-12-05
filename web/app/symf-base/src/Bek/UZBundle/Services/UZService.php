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
        $this->client = new Client(['base_uri' => $baseUrl . $lang . '/']);
    }

    /**
     * @param string $term
     * @return \Psr\Http\Message\StreamInterface
     */
    function getStationsByTerm(string $term)
    {

        $res = $this->client->get( 'purchase/station/?term=' . $term);
        return $res->getBody()->read(2048);

    }

    /**
     * @param $params
     * @return string
     */
    function getTrainsInfo($params)
    {

        $requestParams['form_params'] = $params;
        $resp = $this->client->post('purchase/search/', $requestParams);
        return $resp->getBody()->read(12048);
    }

    public function buildSearchParams(array $params): array
    {
        $validParams = [
            'station_id_from' => $params['stationIdFrom'],
            'station_id_till' => $params['stationIdTill'],
            'station_from' => $params['stationFrom'],
            'station_till' => $params['stationTill'],
            'date_dep' => $params['dateDep']->format('m.d.Y'),
            'time_dep' => $params['timeDep']->format('H:i'),
        ];
        return $validParams;
    }


    public function parseTrainsInfoResponse(string $uzResponse)
    {


        $uzResponse = \GuzzleHttp\json_decode($uzResponse, true);
        $foundTicket=[];

        for ($i = 0; $i < count($uzResponse['value']); $i++) {

            $foundTicket[$i]['from'] = $uzResponse['value'][$i]['from'];
            $foundTicket[$i]['till'] = $uzResponse['value'][$i]['till'];
            $foundTicket[$i]['trainNum'] = $uzResponse['value'][$i]['num'];
            $foundTicket[$i]['travelTime'] = $uzResponse['value'][$i]['travel_time'];
            if (!empty($uzResponse['value'][$i]['types'])) {
                foreach ($uzResponse['value'][$i]['types'] as $type) {

                    $foundTicket[$i][$type['title']] = $type['places'];

                }
            }
        }
        return !empty($foundTicket) ? $foundTicket : false;
    }

}
