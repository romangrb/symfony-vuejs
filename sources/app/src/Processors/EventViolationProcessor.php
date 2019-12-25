<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 25.12.2019
 * Time: 11:16
 */
declare(strict_types=1);

namespace App\Processors;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Services\ContextValidation;
use Interop\Queue\Message;
use Interop\Queue\Context;
use Interop\Queue\Processor;
use Enqueue\Client\CommandSubscriberInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class EventViolationProcessor implements Processor, CommandSubscriberInterface
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
        $event_id = $message->getBody();

        try {
            $event = $this->repository->find($event_id);

            if (empty($event)) throw new \Exception(sprintf('Event Id: [%s] does not exists', $event_id));

            $this->validationEventSentenceProcess($event);

        } catch (\Exception $e) {
            $this->logger->error(print_r($e->getMessage(), true));
            return self::REJECT;
        }

        echo $message->getBody() . '|';

        return self::ACK;
    }

    /**
     * Validation process save validation result into Events
     *
     * @param Event $event
     * @return void
     */
    public function validationEventSentenceProcess(Event $event): void
    {
        $sentence = $event->getDescription();

        if (! empty($sentence)) {
            $violation_array = ContextValidation::contextValidChecker($sentence);
        }

        if (empty($sentence) || empty($violation_array)){
            $event->setIsReviewFailed(false);
        } else {
            $event->setIsReviewFailed(true);
        }

        $this->em->persist($event);
        $this->em->flush();
    }

    /**
     * Subscribed Command
     *
     * @return array
     */
    public static function getSubscribedCommand(): array
    {
        return [
            'processor_name' => 'event-violation-command'
        ];
    }
}