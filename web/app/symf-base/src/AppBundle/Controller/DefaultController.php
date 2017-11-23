<?php

namespace AppBundle\Controller;

use Prophecy\Exception\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Driver\Connection;


/**
 * @Route("/apiss")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

//        try {
//            $dbh = new \PDO('mysql:host=dbsymf;dbname=test', 'root', 'root');
//            foreach ($dbh->query('SELECT * FROM test') as $row) {
//                print_r($row);
//            }
//            $dbh = null;
//        } catch (PDOException $e) {
//            print "Error!: " . $e->getMessage() . "<br/>";
//            die();
//        }
//
//
//        die;
//        phpinfo();

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/dbtest/", name="dbtest")
     */
    public function dbTestAction(Connection $conn)
    {

        $test = $conn->fetchAll('SELECT * FROM test');
        dump($test );


    die;
        $response = new Response(
            'test db',
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );

        return $response;
    }

}
