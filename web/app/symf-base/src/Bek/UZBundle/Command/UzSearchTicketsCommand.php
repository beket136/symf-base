<?php

namespace Bek\UZBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


ini_set('xdebug.var_display_max_depth',200);
ini_set('xdebug.var_display_max_data',20000);
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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $actualRequests = $this->getContainer()->get('doctrine')
            ->getRepository('BekUZBundle:UZSearchRequest')
            ->getActualSRequests();

        $foundTickets = $this->searchForTickets($actualRequests);
        $this->handleFoundTickets($foundTickets, $output);
    }

    /**
     * @param array $actualRequests
     * @return array
     */
    private function searchForTickets(array $actualRequests): array
    {
        $uzservice = $this->getContainer()->get('bek_uz.uzservice');
        $foundTickets = [];

        foreach ($actualRequests as $params){
            $requestParams = $uzservice->buildSearchParams($params);
            try {
                $foundTickets[$params['email']]['email'] = $params['email'];
                $foundTickets[$params['email']][$params['id']]['trains'] = $uzservice->parseTrainsInfoResponse($uzservice->getTrainsInfo($requestParams));
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
     * @param $foundUsersTequests
     * @param OutputInterface $output
     * @return void
     */
    private function handleFoundTickets($foundUsersTequests, OutputInterface $output)
    {

        $twig = $this->getContainer()->get('templating');
        $uzMailer = $this->getContainer()->get('swiftmailer.mailer');

        $adminEmail = $this->getContainer()->hasParameter('uz_admin_email') ?
            $this->getContainer()->getParameter('uz_admin_email') : 'admin@email.com';

        $uzUrl = $this->getContainer()->hasParameter('uz_base_url') ?
            $this->getContainer()->getParameter('uz_base_url') : '';

        $uzLang = $this->getContainer()->hasParameter('uz_lang') ?
            $this->getContainer()->getParameter('uz_lang') : '';

        foreach ($foundUsersTequests as $foundTrips) {

            $twigVars = [
                'uz_url' => $uzUrl . $uzLang,
                'foundTrips' => [],
                'email' => $foundTrips['email'],
            ];
            unset($foundTrips['email']);
            $i = 0;
            foreach ($foundTrips as $trip) {
                $twigVars['foundTrips'][$i] = [
                    'stationFrom' => $trip['stationFrom'],
                    'stationTill' => $trip['stationTill'],
                    'dateDep' => $trip['dateDep'],
                    'timeDep' => $trip['timeDep'],
                ];

                foreach ($trip['trains'] as $train) {
                    $twigVars['foundTrips'][$i]['trains'][$train['trainNum']] = [
                        'from' => $train['from'],
                        'till' => $train['till'],
                        'trainNum' => $train['trainNum'],
                        'travelTime' => $train['travelTime'],
                    ];

                    if (!empty($train['De Luxe / 1-cl. sleeper'])) {
                        $twigVars['foundTrips'][$i]['trains'][$train['trainNum']]['deLuxe'] = $train['De Luxe / 1-cl. sleeper'];
                    }

                    if (!empty($train['Compartment / 2-cl. sleeper'])) {
                        $twigVars['foundTrips'][$i]['trains'][$train['trainNum']]['compartment'] = $train['Compartment / 2-cl. sleeper'];
                    }

                    if (!empty($train['Seating first class'])) {
                        $twigVars['foundTrips'][$i]['trains'][$train['trainNum']]['seatingFirstClass'] = $train['Seating first class'];
                    }

                    if (!empty($train['Seating second class'])) {
                        $twigVars['foundTrips'][$i]['trains'][$train['trainNum']]['seatingSecondClass'] = $train['Seating second class'];
                    }
                 if (!empty($train['Berth / 3-cl. sleeper'])) {
                        $twigVars['foundTrips'][$i]['trains'][$train['trainNum']]['sleeperThirdClass'] = $train['Berth / 3-cl. sleeper'];
                    }
                }
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
                $output->writeln('Email to ' . $twigVars['email'] . ' sent.');
            }
        }
    }
}
