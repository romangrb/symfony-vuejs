<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 28.12.2019
 * Time: 18:23
 */

namespace App\MessageHandler;

use App\Message\CreateEventMessage;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateBookingMessageHandler implements MessageHandlerInterface
{
    /**
     * Event Repository
     *
     * @var $eventRepository
     */
    private $eventRepository;

    /**
     * Entity manager
     *
     * @var $em
     */
    private $em;

    /**
     * Init class
     *
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(EventRepository $eventRepository, EntityManagerInterface $em)
    {
        $this->eventRepository = $eventRepository;
        $this->em = $em;
    }

    /**
     * Invoke event
     *
     * @param CreateEventMessage $bookingMessage
     */
    public function __invoke(CreateEventMessage $bookingMessage)
    {
        $event = $this->eventRepository->find($bookingMessage->getId());
        $event->setName($bookingMessage->getName());

        $this->em->persist($event);
        $this->em->flush();
    }
}