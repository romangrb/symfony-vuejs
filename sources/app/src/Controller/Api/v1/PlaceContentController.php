<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\PlaceContent;
use App\Repository\PlaceContentRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Transformers\ErrorExceptionTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Services\FileUploader;

final class PlaceContentController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    /** @var PlaceContentRepository */
    private $repository;

    /** @var PlaceRepository */
    private $place_repository;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * Initiate class properties
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param PlaceContentRepository $repository
     * @param PlaceRepository $place_repository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        PlaceContentRepository $repository,
        PlaceRepository $place_repository,
        TranslatorInterface $translator
    )
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->repository =  $repository;
        $this->translator = $translator;
        $this->place_repository = $place_repository;
    }

    /**
     * Create content
     *
     * @Rest\Post("/place-content", name="createPlaceContent")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function createPlaceContent(Request $request, ValidatorInterface $validator, FileUploader $fileUploader): JsonResponse
    {
        $place_id = $request->request->get('place_id');

        $content = $request->request->get('content');
        $description = $request->request->get('description');

        $input = [
            'content' => $content,
            'description' => $description,
            'place_id' => $place_id
        ];

        $constraints = new Assert\Collection([
            'content' => [new Assert\Length(['min' => 1]), new Assert\NotBlank],
            'place_id' => [new Assert\NotBlank],
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

//        $file = 'people.html';
//
//        try {
//            $html_content = (new FileUploader('/contents'))->upload($file);
//        } catch (FileException $e){
//            $exceptionData = ErrorExceptionTransformer::transform($e);
//            $translated = $this->translator->trans('file_error ' . $e->getMessage());
//            $this->logger->info(print_r($exceptionData, true));
//            return new JsonResponse($translated,
//                Response::HTTP_UNPROCESSABLE_ENTITY);
//        }
        try {
            $place = $this->place_repository->find($place_id);
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $placeContent = new PlaceContent();
        $placeContent->setDescription($description);
        $placeContent->setIsPublished(false);
        $placeContent->setPlace($place);
        $placeContent->setFilePath('');

        $this->em->persist($placeContent);
        $this->em->flush();
        $data = $this->serializer->serialize($placeContent, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }
}
