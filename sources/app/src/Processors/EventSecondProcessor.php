<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 25.12.2019
 * Time: 11:16
 */
declare(strict_types=1);

namespace App\Processors;

use App\Repository\EventRepository;
use Interop\Queue\Message;
use Interop\Queue\Context;
use Interop\Queue\Processor;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\TopicSubscriberInterface;

class EventSecondProcessor implements Processor, TopicSubscriberInterface
{
    /** @var EventRepository */
    private $repository;

    /** @var $em EntityManagerInterface */
    private $em;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Class initiation
     *
     * @param EventRepository $repository
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EventRepository $repository, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * Process
     * @param Message $message
     * @param Context $session
     * @return string ACK|REJECT|REQUEUE
     */
    public function process(Message $message, Context $session): string
    {
        echo $message->getBody() . '|seconf';

        return self::ACK;
    }

    /**
     * Subscribed Command
     *
     * @return array
     */
    public static function getSubscribedTopics(): array
    {
        return [
            'event-topic'
        ];
    }
}