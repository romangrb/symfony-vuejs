<?php

namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\EventParticipant;
use App\Services\Pagination\PaginationFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiEventController extends AbstractController
{
    /** @var EventRepository */
    private $repository;

    /** @var $translator */
    private $translator;

    /** @var SerializerInterface */
    private $serializer;

    /** @var LoggerInterface */
    private $logger;

    /** @var $em EntityManager */
    private $em;

    /** @const MaxPages */
    const DEFAULT_LIMIT_PER_PAGE = 15;

    /** @const Default page */
    const DEFAULT_PAGE = 1;

    /**
     * Initiate entity manager
     * @param EntityManagerInterface $em
     * @param EventRepository $repository
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $em,
        EventRepository $repository,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        SerializerInterface $serializer
    )
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/event", name="event-index", methods={"GET"})
     * @param Request $request
     * @param PaginationFactory $paginationFactory
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(Request $request, PaginationFactory $paginationFactory): JsonResponse
    {
        $qb = $this->repository->getAllEventsWithSearchBuilder($request->get('name'));

        $paginatedCollection = $paginationFactory
            ->createCollection($qb, $request, 'event-index');

        $json = $this->serializer->serialize($paginatedCollection, JsonEncoder::FORMAT,[]);

        return new JsonResponse($json, Response::HTTP_OK);
    }

    /**
     * @Route("/event", name="add-event", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function add(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $start_time = $request->get('start_time');
        $end_time = $request->get('end_time');

        $event = new Event();
        $event->setName($name);
        $event->setDescription($description);
        $event->setStartTime(\DateTime::createFromFormat( 'Y-m-d H:i:s', $start_time));
        $event->setEndTime(\DateTime::createFromFormat('Y-m-d H:i:s', $end_time));

        $user = $this->getUser();

        $eventParticipant = new EventParticipant();
        $eventParticipant->setEvent($event);
        $eventParticipant->setUser($user);

        $this->em->persist($event);
        $this->em->persist($eventParticipant);

        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }


    /**
     * @Route("/connect/{event_id}", name="connect_to_event", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function connect(Request $request): JsonResponse
    {
        //TODO: provide fn
    }
}
