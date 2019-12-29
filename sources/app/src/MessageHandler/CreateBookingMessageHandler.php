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
    private $eventRepository;

    private $em;

    public function __construct(EventRepository $eventRepository, EntityManagerInterface $em)
    {
        $this->eventRepository = $eventRepository;
        $this->em = $em;
    }
    public function __invoke(CreateEventMessage $bookingMessage)
    {
        $event = $this->eventRepository->find($bookingMessage->getId());
        $event->setName($bookingMessage->getName());

        $this->em->persist($event);
        $this->em->flush();
    }
}