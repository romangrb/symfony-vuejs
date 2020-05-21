<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\Place;
use App\Entity\PlaceContent;
use App\Repository\PlaceContentRepository;
use App\Repository\PlaceRepository;
use App\Requests\AttachPlaceContentTemplateRequestValidator;
use App\Requests\DetachPlaceContentTemplateRequestValidator;
use App\Requests\RenderPlaceContentTemplateRequestValidator;
use App\Requests\UpdatePlaceRequestValidator;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

final class PlaceContentController extends ApiController
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
     * Update template
     *
     * @Route("/place/{id}/template", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function getTemplate(int $id): JsonResponse
    {
        $place = $this->em->getRepository(Place::class)->find($id);

        if (! $place) {
            return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);
        }

        $data = [
            'gjs-css' => '',
            'gjs-html' => ''
        ];

        try {
            $filesystem = new Filesystem();
            $path = $this->getParameter('kernel.project_dir') . "/var/places/$id/";
            $placeContent = $place->getPlaceContent();

            if ($placeContent
                && $placeContent->getFileName()
                && $filesystem->exists("$path/" . $placeContent->getFileName())
            ) {
                $data['gjs-html'] = file_get_contents($path . $placeContent->getFileName());
            }
        } catch (\Exception $exception) {
            return new JsonResponse(json_encode($data), Response::HTTP_OK, [], true);
        }

        return new JsonResponse(json_encode($data), Response::HTTP_OK, [], true);
    }

    /**
     * Update template
     *
     * @Route("/place/{id}/template", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateTemplate(Request $request, int $id): JsonResponse
    {
        $this->transformJsonBody($request);

        $place = $this->em->getRepository(Place::class)->find($id);

        if (! $place) {
            return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);
        }

        $placeContent = $place->getPlaceContent();

        if (! $placeContent) {
            $placeContent = new PlaceContent();
        }

        if (! $placeContent->getFileName()) {
            $placeContent->setFileName(md5(random_bytes(32)) . '.html');
        }

        try {
            $name = $placeContent->getFileName();
            $root = $this->getParameter('kernel.project_dir');
            $path = "var/places/$id/$name";

            $filesystem = new Filesystem();
            $filesystem->dumpFile("$root/$path", $request->request->get('html'));

            $placeContent->setIsPublished(false);
            $place->setPlaceContent($placeContent);

            $this->em->persist($place);
            $this->em->flush();
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse('', Response::HTTP_OK, [], true);
    }

    /**
     * Create/Update template content
     *
     * @Route("/place/{id}/content", name="attachPlaceContentTemplate", methods={"PATCH"})
     * @param Request $request
     * @param AttachPlaceContentTemplateRequestValidator $validatorRequest
     * @return JsonResponse
     */
    public function attachPlaceContentTemplate(Request $request, AttachPlaceContentTemplateRequestValidator $validatorRequest): JsonResponse
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

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) return $validatedRequest;

        try {
            $place = $this->place_repository->find($place_id);
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        if (! $place) return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);

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
     * @Route("/place/{id}/content", name="detachPlaceContentTemplate", methods={"DELETE"})
     * @param Request $request
     * @param DetachPlaceContentTemplateRequestValidator $validatorRequest
     * @return JsonResponse
     */
    public function detachPlaceContentTemplate(Request $request, DetachPlaceContentTemplateRequestValidator $validatorRequest): JsonResponse
    {
        $place_id = $request->get('id');

        $input = ['place_id' => $place_id];

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) return $validatedRequest;

        try {
            $place = $this->place_repository->find($place_id);
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        if (! $place) return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);

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
