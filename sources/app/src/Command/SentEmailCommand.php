<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 13.12.2019
 * Time: 19:33
 */
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SentEmailCommand extends Command
{

    protected $mailerInterface;

    /**
     * Command name
     */
    protected static $defaultName = 'email:send';

    /**
     * Class initiation
     * @param MailerInterface $mailerInterface
     */
    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailerInterface = $mailerInterface;
        parent::__construct();
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setDescription('Command to sent email.')
            ->setHelp('Update events name')
        ;
    }

    /**
     * Execute Command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln([
            '*************************************',
            '*    Send email                     *',
            '*************************************'
        ]);

        $email = (new Email())
            ->from('cc.coder1@gmail.com')
            ->to('cc.coder1@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $this->mailerInterface->send($email);
        $output->writeln([
            '*************************************',
            '*          Email sent               *',
            '*************************************'
        ]);

    }
}