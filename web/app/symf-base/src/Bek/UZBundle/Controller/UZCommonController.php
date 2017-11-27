<?php

namespace Bek\UZBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use function MongoDB\BSON\toJSON;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @Rest\Route("/uz-api")
 *
 */
class UZCommonController extends Controller
{
    /**
     * @Route("/info")
     */
    public function infoAction()
    {
        return $this->render('BekUZBundle:UZCommon:info.html.twig', array(// ...
        ));
    }


    /**
     * @Route("/stations/{term}")
     */
    public function getStationsByTerm(Request $req, $term = '')
    {
        $uzservice = $this->container->get('bek_uz.uzservice');
        $stations = $uzservice->getStationsByTerm($term);
//        $stations = $uzservice->matchTerm($term);


        $response = new Response(
            $stations,
            Response::HTTP_OK,
            array('content-type' => 'application/json')

        );
        return $response;

    }

    /**
     * @Route("/trains_info/")
     */
    public function getTrainsInfo(Request $req)
    {
        $paramsString = $req->getQueryString();
        parse_str($paramsString, $params);

        // http://symf-base.local.dev/uz-api/trains_info/?stationTo=kyiv&stationFrom=kharkov&station_id_from=2200001&station_id_till=2204001&date_dep=15.23.2017
        $uzservice = $this->container->get('bek_uz.uzservice');
        $stations = $uzservice->getTrainsInfo($params);
        $response = new Response(
            $stations,
            Response::HTTP_OK,
            array('content-type' => 'application/json')

        );
        return $response;

    }

}
