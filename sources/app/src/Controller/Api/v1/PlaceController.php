<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\Place;
use App\Entity\PlaceLocation;
use App\Repository\PlaceRepository;
use App\Requests\CreatePlaceRequestValidator;
use App\Requests\UpdatePlaceRequestValidator;
use App\Serializer\Normalizer\PaginationNormalizer;
use App\Serializer\Normalizer\PlaceLocationNormalizer;
use App\Serializer\Normalizer\PlaceNormalizer;
use App\Services\Pagination\PaginationFactory;
use App\Transformers\PaginationTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;

final class PlaceController extends ApiController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    /** @var PlaceRepository */
    private $repository;

    /**
     * Initiate class properties
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param PlaceRepository $repository
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, PlaceRepository $repository)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->repository = $repository;
    }

    /**
     * Create place
     *
     * @Route("/place", name="createPlace", methods={"POST"})
     * @param Request $request
     * @param CreatePlaceRequestValidator $validatorRequest
     * @return JsonResponse
     */
    public function createPlace(Request $request, CreatePlaceRequestValidator $validatorRequest): JsonResponse
    {
        $this->transformJsonBody($request);

        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $locations = $request->request->get('locations');

        $input = [
            'name' => $name,
            'description' => $description
        ];

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) return $validatedRequest;

        $place = new Place();
        $place->setName($name);
        $place->setDescription($description);

        if (! empty($locations)) {
            $placeLocation = new PlaceLocation();
//            TODO multiple location one-to-many instead one-to-one
        }

        $this->em->persist($place);
        $this->em->flush();

        $context = [
            'PlaceLocation' => new PlaceLocationNormalizer,
        ];

        $data = $this->serializer->serialize($place, JsonEncoder::FORMAT, $context);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Update place
     *
     * @Route("/place/{id}", name="updatePlace", methods={"PATCH"})
     * @param Request $request
     * @param UpdatePlaceRequestValidator $validatorRequest
     * @param int $id
     * @return JsonResponse
     */
    public function updatePlace(Request $request, UpdatePlaceRequestValidator $validatorRequest, int $id): JsonResponse
    {
        $this->transformJsonBody($request);

        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $locations = $request->request->get('locations');

        $input = [
            'name' => $name,
            'description' => $description
        ];

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) {
            return $validatedRequest;
        }

        $place = $this->em->getRepository(Place::class)->find($id);

        if (! $place) {
            return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);
        }

        if ($name) $place->setName($name);
        if ($description) $place->setDescription($description);

        if ($locations) {
            $placeLocation = new PlaceLocation();
//            TODO multiple location one-to-many instead one-to-one
        }

        $this->em->persist($place);
        $this->em->flush();

        $context = [
            'PlaceLocation' => new PlaceLocationNormalizer,
        ];

        $data = $this->serializer->serialize($place, JsonEncoder::FORMAT, $context);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Get all places
     *
     * @Route("/places", name="all-places", methods={"GET"})
     * @param Request $request
     * @param PaginationFactory $paginationFactory
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getAllPlaces(Request $request, PaginationFactory $paginationFactory): JsonResponse
    {
        $qb = $this->repository->getPlacesWithSearchBuilder($request);

        $serializer = new Serializer([new PlaceNormalizer]);

        $pagination_serializer = new Serializer([new PaginationNormalizer]);

        $paginatedCollection = $paginationFactory
            ->createCollection($qb, $request, 'all-places');
        ;

        $jsonResponse = PaginationTransformer::normalizeTransform(
            $paginatedCollection,
            $serializer,
            $pagination_serializer,
            JsonEncoder::FORMAT,
            []
        );

        return new JsonResponse($jsonResponse, Response::HTTP_OK);
    }

    /**
     * Delete place
     *
     * @Route("/places/{id}", name="deletePlace", methods={"DELETE"})
     * @param Request $request
     * @IsGranted("ROLE_FOO")
     * @return JsonResponse
     */
    public function deletePlace(Request $request): JsonResponse
    {
        $pace_id = $request->get('id');
        $place = $this->em->getRepository(Place::class)->find($pace_id);

        if (! $place) return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);

        $data = $this->serializer->serialize($place, JsonEncoder::FORMAT);

        $this->em->remove($place);
        $this->em->flush();

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Get place
     *
     * @Route("/place/{id}", name="getPlace", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $place = $this->em->getRepository(Place::class)->find($request->get('id'));

        if (! $place) {
            return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);
        }

        $context = [
            'PlaceLocation' => new PlaceLocationNormalizer,
        ];

        $data = $this->serializer->serialize($place, JsonEncoder::FORMAT, $context);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
