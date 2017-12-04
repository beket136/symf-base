<?php

namespace Bek\UZBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UzSearchTicketsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('uz:search-tickets')
            ->setDescription('Command to check tickets exist');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @Return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {



        $usersEmails = [
            'beket136@gmail.com',
            'asd@mail.com'
        ];

        $actualRequests = $this->getContainer()->get('doctrine')
            ->getRepository('BekUZBundle:UZSearchRequest')
            ->getActualSRequests();

        $usersEmails = $this->searchForTickets($actualRequests);

        $this->handleFoundTickets($usersEmails, $output);
        return 'ok';
    }

    private function searchForTickets(array $actualRequests): array
    {
        $uzservice = $this->getContainer()->get('bek_uz.uzservice');

        $foundTickets = [];

        foreach ($actualRequests as $params){
            $requestParams = $uzservice->buildSearchParams($params);
            try {
                $foundTickets[$params['email']][$params['id']] = $uzservice->parseTrainsInfoResponse($uzservice->getTrainsInfo($requestParams));
                $foundTickets[$params['email']]['email'] = $params['email'];
                $foundTickets[$params['email']][$params['id']]['stationFrom'] = $params['stationFrom'];
                $foundTickets[$params['email']][$params['id']]['stationTill'] = $params['stationTill'];
                $foundTickets[$params['email']][$params['id']]['dateDep'] = $params['dateDep']->format('m.d.Y');
                $foundTickets[$params['email']][$params['id']]['timeDep'] = $params['timeDep']->format('H:i');

            }catch (\Exception $exception){
                echo $exception->getMessage();
            }
        }
        return $foundTickets;
    }

    /**
     * @param $usersEmails
     * @param OutputInterface $output
     * @Return void
     */
    private function handleFoundTickets($foundUsersTequests, OutputInterface $output)
    {

        $twig = $this->getContainer()->get('templating');
        $uzMailer = $this->getContainer()->get('swiftmailer.mailer');

        $adminEmail = $this->getContainer()->hasParameter('uz_admin_email') ?
            $this->getContainer()->getParameter('uz_admin_email') : 'admin@email.com';

        $uzUrl = $this->getContainer()->hasParameter('uz_base_url') ?
            $this->getContainer()->getParameter('uz_base_url') : '';

        foreach ($foundUsersTequests as $foundTrips) {

            $twigVars = [
                'uz_url' => $uzUrl,
                'email' => $foundTrips['email'],
                'foundTrips' => [],
            ];
            unset($foundTrips['email']);

            $i = 0;
            foreach ($foundTrips as $trip) {
                $twigVars['foundTrips'][$i] = [
                    'stationFrom' => $trip['stationFrom'],
                    'stationTill' => $trip['stationTill'],
                    'dateDep' => $trip['dateDep'],
                    'timeDep' => $trip['timeDep'],
                    'from' => $trip['from'],
                    'till' => $trip['till'],
                    'trainNum' => $trip['trainNum'],
                    'travelTime' => $trip['travelTime'],
                ];

                if (!empty($trip['De Luxe / 1-cl. sleeper'])) {
                    $twigVars['foundTrips'][$i]['deLuxe'] = $trip['De Luxe / 1-cl. sleeper'];
                }

                if (!empty($trip['Compartment / 2-cl. sleeper'])) {
                    $twigVars['foundTrips'][$i]['compartment'] = $trip['Compartment / 2-cl. sleeper'];
                }

                if (!empty($trip['Seating first class'])) {
                    $twigVars['foundTrips'][$i]['seatingFirstClass'] = $trip['Seating first class'];
                }

                if (!empty($trip['Seating second class'])) {
                    $twigVars['foundTrips'][$i]['seatingSecondClass'] = $trip['Seating second class'];
                }
//var_dump($twigVars['foundTrips']);die;
                $i++;
            }



            $swiftMessage = new \Swift_Message();
            $swiftMessage->setTo($twigVars['email']);
            $swiftMessage->setFrom($adminEmail);
            $swiftMessage->setBody(
                $twig->render('@uz_bundle_views/Emails/ticket-found.html.twig', $twigVars),
                'text/html'
            );

            if ($uzMailer->send($swiftMessage)) {
                $output->writeln('Email to ' . $twigVars['email'] . 'sent.');
            }
        }
    }
}
