<?php

namespace Bek\UZBundle\Controller;

use Bek\UZBundle\Entity\UZSearchRequest;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\Route("/uz-api")
 */
class UZCommonController extends Controller
{
    /**
     * @Route("/stations/{term}")
     * @Method("GET")
     * @ApiDoc(
     *  description="Get info about trains ",
     * )
     */
    public function getStationsByTerm(string $term): JsonResponse
    {
        $uzservice = $this->container->get('bek_uz.uzservice');
        $data = $uzservice->getStationsByTerm($term);

        return new JsonResponse(['data' => $data],200,['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/trains_info/")
     * @Method("GET")
     *
     * @ApiDoc(
     *  description="Get info about trains ",
     *  parameters={
     *      {"name"="stationFromId", "dataType"="integer", "required"=true, "description"="station From Id"},
     *      {"name"="stationTillId", "dataType"="integer", "required"=true, "description"="station Till Id"},
     *      {"name"="stationFrom", "dataType"="string", "required"=true, "description"="station from"},
     *      {"name"="stationTill", "dataType"="string", "required"=true, "description"="station till"}
     *  }
     * )
     */
    public function getTrainsInfo(Request $req): JsonResponse
    {
        $paramsString = $req->getQueryString();
        parse_str($paramsString, $params);
        $uzservice = $this->container->get('bek_uz.uzservice');
        $data = $uzservice->getTrainsInfo($params);
        return new JsonResponse(['data' => $data]);
    }

    /**
     * @Route("/search_request/")
     * @Method("POST")
     *
     * @ApiDoc(
     *  description="Create a new Object",
     *  parameters={
     *      {"name"="stationFromId", "dataType"="integer", "required"=true, "description"="station From Id"},
     *      {"name"="stationTillId", "dataType"="integer", "required"=true, "description"="station Till Id"},
     *      {"name"="stationFrom", "dataType"="string", "required"=true, "description"="station from"},
     *      {"name"="stationTill", "dataType"="string", "required"=true, "description"="station till"}
     *  }
     * )
     */
    public function addSearchRequest(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $sRequest = new UZSearchRequest();
        if (!$sRequest->setParams($data)) {

            return new JsonResponse(['message' => 'Bad request'], 400);
        }
        $em->persist($sRequest);
        $em->flush();
        if ($sRequest->getId()) {
            $message = ['message' => 'created successfully', 'data' => ['id' => $sRequest->getId()]];
            return new JsonResponse($message, 201);
        }
        return new JsonResponse(['message' => 'Bad request'], 400);
    }

    /**
     * @Route("/search_request/{id}")
     * @Method("PUT")
     *
     * @ApiDoc(
     *  description="Create a new Object",
     *  parameters={
     *      {"name"="stationFromId", "dataType"="integer", "required"=true, "description"="station From Id"},
     *      {"name"="stationTillId", "dataType"="integer", "required"=true, "description"="station Till Id"},
     *      {"name"="stationFrom", "dataType"="string", "required"=true, "description"="station from"},
     *      {"name"="stationTill", "dataType"="string", "required"=true, "description"="station till"}
     *  }
     * )
     */
    public function updateSearchRequest(Request $request, $id)
    {
        $params = [];
        $params['station_from'] = $request->get('station_from');
        $params['station_id_from'] = $request->get('station_id_from');
        $params['station_till'] = $request->get('station_till');
        $params['station_id_till'] = $request->get('station_id_till');
        $params['user_id'] = $request->get('user_id');
        $params['email'] = $request->get('email');
        $params['date_dep'] = $request->get('date_dep');
        $params['time_dep'] = $request->get('time_dep');

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
     *
     * @ApiDoc(
     *  description="Get all search requests"
     * )
     */
    public function getSearchRequests()
    {
        $sRequests = $this->getDoctrine()
            ->getRepository('BekUZBundle:UZSearchRequest')
            ->findAllSRRequests();
        if (!empty($sRequests)) {
            return new JsonResponse(['data' => $sRequests]);
        }
        return new JsonResponse(['message' => 'there is no entities with provided id!', 'data' => []], 404);
    }

    /**
     * @Route("/search_request/{id}")
     * @Method("GET")
     *
     * @ApiDoc(
     *  description="Get all search requests"
     * )
     */
    public function getSearchRequest($id)
    {
        $sRequest = $this->getDoctrine()
            ->getRepository('BekUZBundle:UZSearchRequest')
            ->findSRequest($id);
        if (!empty($sRequest)) {
            return new JsonResponse(['data' => $sRequest]);
        }
        return new JsonResponse(['message' => 'there is no entities with provided id!', 'data' => []], 404);
    }

    /**
     * @Route("/search_request/{id}")
     * @Method("DELETE")
     *
     * @ApiDoc(
     *  description="Get all search requests",
     *  parameters={
     *       {"name"="Id", "dataType"="integer", "required"=true, "description"="Id os search request to delete"},
     *  }
     * )
     */
    public function deleteSRequest($id)
    {
        $em = $this->getDoctrine()->getManager();
        $sRequest = $this->getDoctrine()
            ->getRepository('BekUZBundle:UZSearchRequest')->find($id);
        if ($sRequest != null) {
            $em->remove($sRequest);
            $em->flush();
            return new JsonResponse(['message' => 'Deleted!']);
        }
        return new JsonResponse(['message' => 'Fail'], 404);
    }

}
