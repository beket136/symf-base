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
     * @return array
     */
    function getStationsByTerm(string $term): array
    {
        $res = $this->client->get('purchase/station/?term=' . $term);
        return \GuzzleHttp\json_decode($res->getBody()->getContents(), true);
    }

    /**
     * @param $params
     * @return array
     */
    function getTrainsInfo($params): array
    {
        $requestParams['form_params'] = $params;
        $resp = $this->client->post('purchase/search/', $requestParams);
        return \GuzzleHttp\json_decode($resp->getBody()->getContents(), true);
    }

    /**
     * @param array $params
     * @return array
     */
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


    /**
     * @param array $uzResponse
     * @return array|bool
     */
    public function parseTrainsInfoResponse(array $uzResponse)
    {
        $foundTicket = [];

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
