<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\PlaceAttachment;
use App\Repository\PlaceAttachmentRepository;
use App\Repository\PlaceContentRepository;
use App\Repository\PlaceRepository;
use App\Requests\CreatePlaceAttachmentRequestValidator;
use App\Requests\DeletePlaceAttachmentRequestValidator;
use App\Requests\ListPlaceAttachmentRequestValidator;
use App\Requests\ShowPlaceAttachmentRequestValidator;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Transformers\ErrorExceptionTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;

final class PlaceAttachmentController extends AbstractController
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
     * @param PlaceAttachmentRepository $repository
     * @param PlaceRepository $place_repository
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        PlaceAttachmentRepository $repository,
        PlaceRepository $place_repository,
        TranslatorInterface $translator,
        LoggerInterface $logger
    )
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->repository =  $repository;
        $this->translator = $translator;
        $this->place_repository = $place_repository;
        $this->logger = $logger;
    }

    /**
     * List place files content
     *
     * @Route("/place/{id}/attachments", name="listPlaceAttachments", methods={"GET"})
     * @param Request $request
     * @param ListPlaceAttachmentRequestValidator $validatorRequest
     * @return JsonResponse
     */
    public function listPlaceAttachments(
        Request $request,
        ListPlaceAttachmentRequestValidator $validatorRequest): JsonResponse
    {
        $place_id = $request->get('id');

        $input = ['place_id' => $place_id];

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) return $validatedRequest;

        try {
            $place = $this->place_repository->find($place_id);

            $placeAttachment = $place->getPlaceAttachment();

        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($placeAttachment, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Show place files content
     *
     * @Route("/place/attachment/{id}", name="showPlaceAttachments", methods={"GET"})
     * @param Request $request
     * @param ShowPlaceAttachmentRequestValidator $validatorRequest
     * @return JsonResponse
     */
    public function showPlaceAttachments(
        Request $request,
        ShowPlaceAttachmentRequestValidator $validatorRequest): JsonResponse
    {
        $place_attachment_id = $request->get('id');

        $input = ['place_attachment_id' => $place_attachment_id];

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) return $validatedRequest;

        try {
            $place_attachment = $this->repository->find($place_attachment_id);

        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($place_attachment, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Attach entity for to place
     *
     * @Route("/place/{id}/attachment", name="createPlaceAttachment", methods={"POST"})
     * @param Request $request
     * @param CreatePlaceAttachmentRequestValidator $validatorRequest
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function createPlaceAttachment(Request $request, CreatePlaceAttachmentRequestValidator $validatorRequest, FileUploader $fileUploader): JsonResponse
    {
        $place_id = $request->get('id');
        $attachment_name = $request->get('name');
        $attachment = $request->files->get('attachment');

        $input = [
            'place_id' => $place_id,
            'attachment' => $attachment,
        ];

        if ($attachment_name) {
            $input['name'] = $attachment_name;
        }

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

        $file_name = $attachment_name ?? $attachment->getClientOriginalName();

        try {
            $file_path = $fileUploader->upload($attachment);
        } catch (FileException $e){
            $exceptionData = ErrorExceptionTransformer::transform($e);
            $translated = $this->translator->trans('file_error ' . $e->getMessage());
            $this->logger->info(print_r($exceptionData, true));
            return new JsonResponse($translated,
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $placeAttachment = new PlaceAttachment();
        $placeAttachment->setPlace($place);
        $placeAttachment->setName($file_name);
        $placeAttachment->setFilePath($file_path);

        $this->em->persist($placeAttachment);
        $this->em->flush();

        $data = $this->serializer->serialize($placeAttachment, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Delete place attachment
     *
     * @Route("/place/attachment/{id}", name="deletePlaceAttachment", methods={"DELETE"})
     * @param Request $request
     * @param DeletePlaceAttachmentRequestValidator $validatorRequest
     * @return JsonResponse
     */
    public function deletePlaceAttachment(Request $request, DeletePlaceAttachmentRequestValidator $validatorRequest): JsonResponse
    {
        $attachment_id = $request->get('id');

        $input = [
            'attachment_id' => $attachment_id
        ];

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) return $validatedRequest;

        try {
            $placeAttachment = $this->repository->find($attachment_id);
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $this->logger->error(print_r($message, true));
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        if (! $placeAttachment) return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);

        $file_root_path = (string) $placeAttachment->getFilePath();

        $filesystem = new Filesystem();

        if ($filesystem->exists($file_root_path)) $filesystem->remove($file_root_path);

        $this->em->remove($placeAttachment);
        $this->em->flush();

        return new JsonResponse('', Response::HTTP_OK, [], true);
    }
}
