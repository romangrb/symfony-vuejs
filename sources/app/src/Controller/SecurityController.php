<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Requests\RequestValidator;
use App\Transformers\ErrorExceptionTransformer;
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
     * @Route("/user/info", name="user-info", methods={"GET"})
     * @return JsonResponse
     */
    public function getInfo(): JsonResponse
    {
        try {
            $user = $this->repository->find('08701b95-5c4d-4841-aabd-43767ff4fd19');
        } catch (\Exception $e) {
            $exceptionData = ErrorExceptionTransformer::transform($e);
            $this->logger->info(print_r($exceptionData, true));
            return new JsonResponse($e->getMessage(),
                Response::HTTP_BAD_REQUEST);
        }

        $response = $user->getAttributes();

        return new JsonResponse($response,  Response::HTTP_OK);
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
