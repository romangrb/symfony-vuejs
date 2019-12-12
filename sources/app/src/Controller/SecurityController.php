<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Requests\RequestValidator;
use App\Transformers\ErrorExceptionTransformer;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Files;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/api")
 */
final class SecurityController extends AbstractController
{
    /** @var UserRepository */
    private $repository;

    /** @var SerializerInterface */
    private $serializer;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        UserRepository $repository,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     * @Route("/security/login", name="login")
     */
    public function loginAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $userClone = clone $user;
        $userClone->setPassword('');
        $data = $this->serializer->serialize($userClone, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/security/test", name="test")
     * @param Request $request
     * @return JsonResponse
     */
    public function test(Request $request): JsonResponse
    {
        $validator = (new RequestValidator($request))->validate();

        if (! $validator->isValid()) {
            return new JsonResponse($validator->getErrors(), Response::HTTP_UNPROCESSABLE_ENTITY, []);
        }

        return new JsonResponse(null, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/security/avatar", name="avatar", methods={"POST"})
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function avatar(
        Request $request,
        EntityManagerInterface $em,
        FileUploader $fileUploader): JsonResponse
    {
        $file = $request->files->get('file');

        try {
            $user = $this->repository->find('08701b95-5c4d-4841-aabd-43767ff4fd19');
        } catch (\Exception $e){
            $exceptionData = ErrorExceptionTransformer::transform($e);
            $this->logger->info(print_r($exceptionData, true));
            return new JsonResponse($e->getMessage(),
                Response::HTTP_BAD_REQUEST);
        }

        if (empty($file)) {
            $translated = $this->translator->trans('no_file');
            $this->logger->info($translated);
            return new JsonResponse($translated,
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $file_path = $fileUploader->upload($file);
        } catch (FileException $e){
            $exceptionData = ErrorExceptionTransformer::transform($e);
            $translated = $this->translator->trans('file_error ' . $e->getMessage());
            $this->logger->info(print_r($exceptionData, true));
            return new JsonResponse($translated,
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file_exists = $user->getAvatarFile();

        if ($file_exists) {
            $em->remove($file_exists);
            $em->flush();
        }

        $fileEntity = new Files();
        $fileEntity
            ->setName($file->getClientOriginalName())
            ->setPath($file_path)
        ;

        $em->persist($fileEntity);

        $user->setAvatarFile($fileEntity);
        $em->flush();

        return new JsonResponse($this->translator->trans($this->translator->trans('success')),  Response::HTTP_OK);
    }

    /**
     * @throws RuntimeException
     *
     * @Route("/security/logout", name="logout")
     */
    public function logoutAction(): void
    {
        throw new RuntimeException('This should not be reached!');
    }
}
