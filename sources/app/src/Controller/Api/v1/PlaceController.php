<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class PlaceController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * Initiate class properties
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * Create place
     *
     * @Rest\Post("/place", name="createPlace")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createPlace(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $name = $request->get('name');
        $description = $request->get('description');

        $input = [
            'name' => $name,
            'description' => $description,
        ];

        $constraints = new Assert\Collection([
            'name' => [new Assert\Length(['min' => 3, 'max' => 255]), new Assert\NotBlank],
            'description' => [new Assert\Length(['min' => 0, 'max' => 255])]
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

        $place = new Place();
        $place->setName($name);
        $place->setDescription($description);
        $this->em->persist($place);
        $this->em->flush();
        $data = $this->serializer->serialize($place, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Update place
     *
     * @Rest\Patch("/place/{id}", name="updatePlace")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @IsGranted("ROLE_FOO")
     */
    public function updatePlace(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $place_id = $request->get('id');

        $input = [
            'name' => $name,
            'description' => $description,
        ];

        $constraints = new Assert\Collection([
            'name' => [new Assert\Length(['min' => 3, 'max' => 255])],
            'description' => [new Assert\Length(['min' => 0, 'max' => 255])],
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

        $place = $this->em->getRepository(Place::class)->find($place_id);
        $place->setName($name);
        $place->setDescription($description);
        $this->em->persist($place);
        $this->em->flush();

        $data = $this->serializer->serialize($place, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Get all places
     *
     * @Rest\Get("/places", name="getAllPlaces")
     */
    public function getAllPlaces(): JsonResponse
    {
        $places = $this->em->getRepository(Place::class)->findBy([], ['id' => 'DESC']);
        $data = $this->serializer->serialize($places, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Delete place
     *
     * @Rest\Delete("/places/{id}", name="deletePlace")
     * @param Request $request
     * @IsGranted("ROLE_FOO")
     * @return JsonResponse
     */
    public function deletePlace(Request $request): JsonResponse
    {
        $pace_id = $request->get('id');
        $place = $this->em->getRepository(Place::class)->find($pace_id);

        $data = $this->serializer->serialize($place, JsonEncoder::FORMAT);

        $this->em->remove($place);
        $this->em->flush();

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
