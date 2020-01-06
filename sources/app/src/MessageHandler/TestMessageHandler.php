<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 28.12.2019
 * Time: 18:23
 */
namespace App\MessageHandler;

use App\Message\TestMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestMessageHandler implements MessageHandlerInterface
{
    /**
     * Mailer interface
     *
     * @var $mailerInterface
     */
    protected $mailerInterface;

    /**
     * Init class
     *
     * @param MailerInterface $mailerInterface
     */
    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailerInterface = $mailerInterface;
    }

    /**
     * Sent message
     *
     * @param TestMessage $testMessage
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function __invoke(TestMessage $testMessage)
    {
        $email = (new Email())
            ->from('cc.coder1@gmail.com')
            ->to('cc.coder1@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html("<p>{$testMessage->getContent()}</p>");

        $this->mailerInterface->send($email);
    }
}