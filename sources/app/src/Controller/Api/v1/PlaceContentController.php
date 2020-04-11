<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\PlaceContent;
use App\Repository\PlaceContentRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
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
use Twig\Environment;
use Symfony\Component\Security\Core\Security;

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
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        PlaceContentRepository $repository,
        PlaceRepository $place_repository,
        TranslatorInterface $translator,
        Environment $twig,
        LoggerInterface $logger
    )
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->repository =  $repository;
        $this->translator = $translator;
        $this->place_repository = $place_repository;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    /**
     * Show place content
     *
     * @Rest\Get("/place/{id}/template", name="showTemplateContent")
     * @param Request $request
     * @param Security $security
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function showTemplateContent(
        Request $request,
        Security $security,
        ValidatorInterface $validator): JsonResponse
    {
        $place_id = $request->get('id');

        $input = ['place_id' => $place_id];

        $constraints = new Assert\Collection([
            'place_id' => [new Assert\NotBlank]
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

        $file_path = $place->getPlaceContent()->getFilePath();
        $templateVariablesHash = $security->getUser()->getTemplateVariablesHash();

        try {
            $htmlContents = $this->twig->render($file_path, $templateVariablesHash);
        } catch (\Twig\Error\Error $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($htmlContents, Response::HTTP_OK, [], true);
    }

    /**
     * Render template
     *
     * @Rest\Post("/place/{id}/template/render", name="renderPlaceContentTemplateFromString")
     * @param Request $request
     * @param Security $security
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function renderPlaceContentTemplateFromString(
        Request $request,
        Security $security,
        ValidatorInterface $validator): JsonResponse
    {
        $place_id = $request->get('id');
        $content = $request->get('content');

        $input = [
            'place_id'=> $place_id,
            'content' => $content
        ];

        $constraints = new Assert\Collection([
            'place_id' => [new Assert\NotBlank, new Assert\Length(['min' => 1])],
            'content' => [new Assert\NotBlank]
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

        $templateVariablesHash = $security->getUser()->getTemplateVariablesHash();

        try {
            $template = $this->twig->createTemplate($content);
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        try {
            $result = $template->render($templateVariablesHash);
        } catch (\Twig\Error\Error $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($result, Response::HTTP_OK, [], true);
    }

    /**
     * Create/Update template content
     *
     * @Rest\Patch("/place/{id}/content", name="attachPlaceContentTemplate")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function attachPlaceContentTemplate(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $place_id = $request->get('id');
        $content  = $request->get('content');
        $description  = $request->get('description');
        $is_published = $request->get('is_published');

        $input = [
            'content' => $content,
            'description' => $description,
            'place_id' => $place_id,
            'is_published' => $is_published
        ];

        $constraints = new Assert\Collection([
            'content' => [new Assert\Length(['min' => 1]), new Assert\NotBlank],
            'place_id' => [new Assert\NotBlank],
            'description' => [new Assert\Length(['min' => 0, 'max' => 255])],
            'is_published' => [new Assert\Length(['min' => 0, 'max' => 1])]
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

        $template_dir = $this->getParameter('public_template_path');
        $file_path = (string) sprintf('%s_%s.html.twig', 'content', $place_id);
        $filesystem = new Filesystem();
        $file_root_path = (string) sprintf('%s/%s', $template_dir, $file_path);

        try {
            $filesystem->dumpFile($file_root_path, $content);
        } catch (IOExceptionInterface $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        if ($placeContent = $place->getPlaceContent()) {
        } else {
            $placeContent = new PlaceContent();
            $placeContent->setPlace($place);
        };

        if (! is_null($description)) $placeContent->setDescription($description);
        if (! is_null($is_published)) $placeContent->setIsPublished((boolean) $is_published);
        if (! is_null($file_path)) $placeContent->setFilePath($file_path);

        $this->em->persist($placeContent);
        $this->em->flush();

        $data = $this->serializer->serialize($placeContent, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Detach template content
     *
     * @Rest\Delete("/place/{id}/content", name="detachPlaceContentTemplate")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function detachPlaceContentTemplate(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $place_id = $request->get('id');

        $input = ['place_id' => $place_id];

        $constraints = new Assert\Collection(['place_id' => [new Assert\NotBlank]]);

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

        $placeContent = $place->getPlaceContent();

        if (! $placeContent) return new JsonResponse([], Response::HTTP_OK, [], true);

        $file_path = (string) $placeContent->getFilePath();
        $template_dir = $this->getParameter('public_template_path');
        $file_root_path = (string) sprintf('%s/%s', $template_dir, $file_path);

        $filesystem = new Filesystem();

        if ($filesystem->exists($file_root_path)) $filesystem->remove($file_root_path);

        $placeContent->setFilePath('');

        $this->em->persist($placeContent);
        $this->em->flush();

        $data = $this->serializer->serialize($placeContent, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }
}
