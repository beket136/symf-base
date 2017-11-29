<?php

namespace Bek\UZBundle\Controller;

use Bek\UZBundle\Entity\UZSearchRequest;
use FOS\RestBundle\Controller\Annotations as Rest;
use function MongoDB\BSON\toJSON;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Route("/stations/{term}")
     * @Method("GET")
     */
    public function getStationsByTerm(Request $req, $term = ''): JsonResponse
    {
        $uzservice = $this->container->get('bek_uz.uzservice');
        $data = $uzservice->getStationsByTerm($term);
        $message = ['code' => 200, 'data' => $data];
        return JsonResponse($message);
    }

    /**
     * @Route("/trains_info/")
     * @Method("GET")
     * // http://symf-base.local.dev/uz-api/trains_info/?stationTo=kyiv&stationFrom=kharkiv&station_id_from=2204001&station_id_till=2200001&date_dep=01.02.2018
     */
    public function getTrainsInfo(Request $req): JsonResponse
    {
        $paramsString = $req->getQueryString();
        parse_str($paramsString, $params);
        $uzservice = $this->container->get('bek_uz.uzservice');
        $data = $uzservice->getTrainsInfo($params);

        $message = ['code' => 200, 'data' => $data];
        return JsonResponse($message);

    }

    /**
     * @Route("/search_request/")
     * @Method("POST")
     */
    public function addSearchRequest(Request $request): JsonResponse
    {
        $message = ['code' => 404, 'message' => 'Fail'];
        $em = $this->getDoctrine()->getManager();

        $data = $request->request->all();
//        var_dump($data);

        $sRequest = new UZSearchRequest();
        $sRequest->setUserId(2);
        $sRequest->setEmail('tes2t44@est.com');
        $sRequest->setStationFrom('Kharkiv');
        $sRequest->setStationTill('Kyiv');
        $sRequest->setStationIdFrom(2200001);
        $sRequest->setStationIdTill(2204001);
        $sRequest->setTimeDep(new \DateTime('00:00:00'));
        $sRequest->setDateDep(new \DateTime('2018-02-01', new \DateTimeZone('UTC')));

        $em->persist($sRequest);
        $em->flush();
        if ($sRequest->getId()) {
            $message = ['message' => 'created successfuly', 'data' => ['id' => $sRequest->getId()]];
            $httpCode = 201;

        }
        return new JsonResponse($message, $httpCode);
    }

    /**
     * @Route("/search_request/{id}")
     * @Method("PATCH")
     */
    public function updateSearchRequest(Request $request, $id)
    {
        $params = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        if ($sRequest = $em->getRepository('BekUZBundle:UZSearchRequest')->find($id)) {

            if (!$sRequest->setParams($params)) {
                return new JsonResponse(['message' => 'Bad request'], 400);
            }
            $em->flush();
            return new JsonResponse(['message' => 'Updated successfully.'], 200);

        }
        return new JsonResponse(['message' => 'Resource not found.'], 404);
    }


    /**
     * @Route("/search_requests/")
     * @Method("GET")
     */
    public function getSearchRequests()
    {

        $sRequests = $this->getDoctrine()
            ->getRepository('BekUZBundle:UZSearchRequest')
            ->findAllSRRequests();
        return new JsonResponse($sRequests);
        if ($sRequest != null) {


            $em->flush();
            $message = ['code' => 200, 'message' => 'Deleted!'];
        }
        return new JsonResponse($message);
    }

    /**
     * @Route("/search_request/{id}")
     * @Method("GET")
     */
    public function getSearchRequest($id)
    {

        $sRequest = $this->getDoctrine()
            ->getRepository('BekUZBundle:UZSearchRequest')
            ->findSRequest($id);

        return new JsonResponse($sRequest);

    }

    /**
     * @Route("/search_request/{id}")
     * @Method("DELETE")
     */
    public function deleteSRequest($id)
    {
        $message = ['code' => 404, 'message' => 'Fail'];
        $em = $this->getDoctrine()->getManager();
        $sRequest = $this->getDoctrine()
            ->getRepository('BekUZBundle:UZSearchRequest')->find($id);
        if ($sRequest != null) {
            $em->remove($sRequest);
            $em->flush();
            $message = ['code' => 200, 'message' => 'Deleted!'];
        }
        return new JsonResponse($message);
    }



}
