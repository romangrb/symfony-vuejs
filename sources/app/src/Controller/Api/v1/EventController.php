<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\Event;
use App\Entity\EventParticipant;
use App\Repository\EventParticipantRepository;
use App\Serializer\Normalizer\EventNormalizer;
use App\Serializer\Normalizer\PaginationNormalizer;
use App\Serializer\Normalizer\ParticipantNormalizer;
use App\Serializer\Normalizer\UserNormalizer;
use App\Services\Pagination\PaginationFactory;
use App\Transformers\ErrorExceptionTransformer;
use App\Transformers\PaginationTransformer;
use Symfony\Component\HttpClient\HttpClient;
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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Serializer;

class EventController extends AbstractController
{
    /** @var EventRepository */
    private $repository;

    /** @var EventParticipantRepository */
    private $event_participants_repository;

    /** @var $translator */
    private $translator;

    /** @var SerializerInterface */
    private $serializer;

    /** @var LoggerInterface */
    private $logger;

    /** @var $em EntityManagerInterface */
    private $em;

    /** @const MaxPages */
    const DEFAULT_LIMIT_PER_PAGE = 15;

    /** @const Default page */
    const DEFAULT_PAGE = 1;

    /**
     * Initiate entity manager
     * @param EntityManagerInterface $em
     * @param EventRepository $repository
     * @param EventParticipantRepository $event_participants_repository
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param SerializerInterface $serializer
     */
    public function __construct(
        EntityManagerInterface $em,
        EventRepository $repository,
        EventParticipantRepository $event_participants_repository,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        SerializerInterface $serializer
    )
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->event_participants_repository = $event_participants_repository;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->serializer = $serializer;
    }

    /**
     * Index Event
     *
     * @Route("/event", name="event-index", methods={"GET"})
     * @param Request $request
     * @param PaginationFactory $paginationFactory
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function index(Request $request, PaginationFactory $paginationFactory): JsonResponse
    {
        $qb = $this->repository->getAllEventsWithSearchBuilder($request->get('name'));

        $serializer = new Serializer([new EventNormalizer]);

        $context = [
            'ParticipantNormalizer' => new ParticipantNormalizer,
            'UserNormalizer' => new UserNormalizer
        ];

        $pagination_serializer = new Serializer([new PaginationNormalizer]);

        $paginatedCollection = $paginationFactory
            ->createCollection($qb, $request, 'event-index');
        ;

        $jsonResponse = PaginationTransformer::normalizeTransform(
            $paginatedCollection,
            $serializer,
            $pagination_serializer,
            JsonEncoder::FORMAT,
            $context
        );

        return new JsonResponse($jsonResponse, Response::HTTP_OK);
    }

    /**
     * @Route("/event", name="add-event", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function add(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $start_time = $request->get('start_time');
        $end_time = $request->get('end_time');

        $input = [
            'name' => $name,
            'description' => $description,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];

        $constraints = new Assert\Collection([
            'name' => [new Assert\Length(['min' => 2, 'max' => 100]), new Assert\NotBlank],
            'description' => [new Assert\Length(['min' => 0])],
            'start_time' => [new Assert\DateTime(['format'=>'Y-m-d H:i:s', 'message'=>'Format should be Y-m-d H:i:s ex. 2019-12-22 12:35:07'])],
            'end_time' => [new Assert\DateTime(['format'=>'Y-m-d H:i:s', 'message'=>'Format should be Y-m-d H:i:s ex. 2019-12-22 12:35:07'])],
        ]);

        $violations = $validator->validate($input, $constraints);

        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                    $violation->getPropertyPath(),
                    $violation->getMessage());
            }
            return new JsonResponse($errorMessages, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $event = new Event();
        $event->setName($name);
        $event->setDescription($description);
        $event->setStartTime(\DateTime::createFromFormat( 'Y-m-d H:i:s', $start_time));
        $event->setEndTime(\DateTime::createFromFormat('Y-m-d H:i:s', $end_time));

        $this->em->persist($event);
        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }


    /**
     * @Route("/connect/{event_id}", name="connect_to_event", methods={"GET"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function connect(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $event_id = $request->get('event_id');

        $input = ['event_id' => $event_id];

        $constraints = new Assert\Collection([
            'event_id' => [new Assert\Length(['min' => 0, 'max' => 100]), new Assert\NotBlank],
        ]);

        $violations = $validator->validate($input, $constraints);

        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                    $violation->getPropertyPath(),
                    $violation->getMessage());
            }
            return new JsonResponse($errorMessages, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $event = $this->repository->find($event_id);
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();

        $eventParticipants = $this->event_participants_repository->findBy(
            ['event' => $event->getId(), 'user' => $user->getId()],
            [],
            1
        );

        if (! empty($eventParticipants)) return new JsonResponse(null, Response::HTTP_OK);

        $eventParticipant = new EventParticipant();
        $eventParticipant->setEvent($event);
        $eventParticipant->setUser($user);

        $event->addEventParticipant($eventParticipant);
        $this->em->persist($eventParticipant);

        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @Route("/disconnect/{event_id}", name="disconnect_to_event", methods={"GET"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function disconnect(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $event_id = $request->get('event_id');

        $input = ['event_id' => $event_id];

        $constraints = new Assert\Collection([
            'event_id' => [new Assert\Length(['min' => 0, 'max' => 100]), new Assert\NotBlank],
        ]);

        $violations = $validator->validate($input, $constraints);

        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                    $violation->getPropertyPath(),
                    $violation->getMessage());
            }
            return new JsonResponse($errorMessages, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $event = $this->repository->find($event_id);
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();

        $eventParticipants = $this->event_participants_repository->findBy(
            ['event' => $event->getId(), 'user' => $user->getId()],
            [],
            1
        );

        if (empty($eventParticipants)) return new JsonResponse(null, Response::HTTP_OK);

        $this->em->remove($eventParticipants[0]);
        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @Route("/send-data", name="send-data", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function sentData(Request $request): JsonResponse
    {
        $client = HttpClient::create();

        $content = $request->getContent();

        $response = $client->request('POST', 'http://192.168.3.72/api/store-data', [
            'body' => $content,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
