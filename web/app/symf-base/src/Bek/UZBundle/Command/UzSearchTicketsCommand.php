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

        /////
//        $response = $uzservice->getTrainsInfo(
//            [
//                'station_id_from' => '2200001',
//                'station_id_till' => '2204001',
//                'station_from' => 'Kyiv',
//                'station_till' => 'Kharkiv',
//                'date_dep' => '12.09.2017',
//                'time_dep' => '00:00:00',
//            ]
//        );

        ////////
        $usersEmails = [];

        foreach ($actualRequests as $params){

            $params = $uzservice->buildSearchParams($params);
            $response = $uzservice->getTrainsInfo($params);
            var_dump($response);die;

            if( false ){ //TODO parse response and add condition
                $usersEmails[] = $actualRequests['email'];
            }
            //TODO  if found add email and maybe information about existing tickets (trains , ticket types , qty) to

        }
        return $usersEmails;
    }

    /**
     * @param $usersEmails
     * @param OutputInterface $output
     * @Return void
     */
    private function handleFoundTickets($usersEmails, OutputInterface $output)
    {

        $twig = $this->getContainer()->get('templating');
        $uzMailer = $this->getContainer()->get('swiftmailer.mailer');

        $adminEmail = $this->getContainer()->hasParameter('uz_admin_email') ?
            $this->getContainer()->getParameter('uz_admin_email') : 'admin@email.com';

        $uzUrl = $this->getContainer()->hasParameter('uz_base_url') ?
            $this->getContainer()->getParameter('uz_base_url') : '';

        foreach ($usersEmails as $email) {

            $twigVars = [
                'uz_url' => $uzUrl,
                'email' => $email
            ];

            $swiftMessage = new \Swift_Message();
            $swiftMessage->setTo($email);
            $swiftMessage->setFrom($adminEmail);
            $swiftMessage->setBody(
                $twig->render('@uz_bundle_views/Emails/ticket-found.html.twig', $twigVars),
                'text/html'
            );

            if ($uzMailer->send($swiftMessage)) {
                $output->writeln('Email to ' . $email . 'sent.');
            }
        }
    }
}
