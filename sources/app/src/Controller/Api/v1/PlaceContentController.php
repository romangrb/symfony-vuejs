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
use Twig\Environment;
use App\Services\FileUploader;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

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

    /** @var Twig */
    private $twig;

    /**
     * Initiate class properties
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param PlaceContentRepository $repository
     * @param PlaceRepository $place_repository
     * @param TranslatorInterface $translator
     * @param Environment $twig
     */
    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        PlaceContentRepository $repository,
        PlaceRepository $place_repository,
        TranslatorInterface $translator,
        Environment $twig
    )
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->repository =  $repository;
        $this->translator = $translator;
        $this->place_repository = $place_repository;
        $this->twig = $twig;
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

        try {
            $place = $this->place_repository->find($place_id);
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $filesystem = new Filesystem();
        $file_origin_name = (string) sprintf('%s_%s.html', 'content', $place_id);

        try {
            $filesystem->dumpFile($file_origin_name, $content);
        } catch (IOExceptionInterface $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $placeContent = new PlaceContent();
        $placeContent->setDescription($description);
        $placeContent->setIsPublished(false);
        $placeContent->setPlace($place);
        $placeContent->setFilePath($file_origin_name);

        $this->em->persist($placeContent);
        $this->em->flush();
        $data = $this->serializer->serialize($placeContent, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Update content
     *
     * @Rest\Patch("/place-content", name="createPlaceContent")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param FileUploader $fileUploader
     * @return JsonResponse
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function updatePlaceContent(
        Request $request,
        ValidatorInterface $validator,
        FileUploader $fileUploader): JsonResponse
    {
        $place_id = $request->request->get('place_id');

        $content = $request->request->get('content');
        $description = $request->request->get('description');
        $is_published = $request->request->get('is_published');

        $input = [
            'content' => $content,
            'description' => $description,
            'place_id' => $place_id
        ];

        $constraints = new Assert\Collection([
            'content' => [new Assert\Length(['min' => 1])],
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

        try {
            $place = $this->place_repository->find($place_id);
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $filesystem = new Filesystem();
        $file_origin_name = (string) sprintf('%s_%s.html.twig', 'content', $place_id);
//        $projectDir = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $file_origin_name;
//
////dd($projectDir);
////        $loader = $this->twig->getLoader();
////dd($projectDir);
////        if ($loader->exists($projectDir)) {
////            dd(1);
////        }else{
////            dd(2);
////        }
//
//        $htmlContents = $this->twig->render($projectDir, [
//            'category' => 11,
//            'promotion' => 22,
//        ]);
//
//        return new JsonResponse($htmlContents, Response::HTTP_CREATED, [], true);

        try {
            $filesystem->dumpFile($file_origin_name, $content);
        } catch (IOExceptionInterface $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $placeContent = $place->getPlaceContent();
        $placeContent->setDescription($description);
        $placeContent->setIsPublished(false);
        $placeContent->setPlace($place);
        $placeContent->setFilePath($file_origin_name);

        $this->em->persist($placeContent);
        $this->em->flush();
        $data = $this->serializer->serialize($placeContent, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Get all place-contents
     *
     * @Rest\Get("/place-contents", name="getAllPlaceContents")
     */
    public function getAllPlaceContents(): JsonResponse
    {
        $places = $this->em->getRepository(PlaceContent::class)->findBy([], ['id' => 'DESC']);
        $data = $this->serializer->serialize($places, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
