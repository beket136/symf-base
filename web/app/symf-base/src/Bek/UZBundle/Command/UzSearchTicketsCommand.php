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

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $usersEmails = [
            'beket136@gmail.com',
            'asd@mail.com'
        ];

        $this->handleFoundTickets($usersEmails, $output);
    }

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
