<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Rest\Route("/api")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class PostController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Post("/posts", name="createPost")
     * @param Request $request
     * @IsGranted("ROLE_FOO")
     * @return JsonResponse
     */
    public function createAction(Request $request): JsonResponse
    {
        $message = $request->request->get('message');
        if (empty($message)) {
            throw new BadRequestHttpException('message cannot be empty');
        }
        $post = new Post();
        $post->setMessage($message);
        $this->em->persist($post);
        $this->em->flush();
        $data = $this->serializer->serialize($post, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * @Rest\Put("/post/{id}", name="createPost")
     * @param Request $request
     * @IsGranted("ROLE_FOO")
     * @return JsonResponse
     */
    public function updateAction(Request $request): JsonResponse
    {
        $message = $request->request->get('message');
        $post_id = $request->get('id');
        if (empty($message) || empty($post_id)) {
            throw new BadRequestHttpException('message or id cannot be empty');
        }

        $post = $this->em->getRepository(Post::class)->find($post_id);
        $post->setMessage($message);
        $this->em->persist($post);
        $this->em->flush();

        $data = $this->serializer->serialize($post, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * @Rest\Get("/posts", name="findAllPosts")
     */
    public function findAllAction(): JsonResponse
    {
        $posts = $this->em->getRepository(Post::class)->findBy([], ['id' => 'DESC']);
        $data = $this->serializer->serialize($posts, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Delete("/post/{id}", name="deletePost")
     * @param Request $request
     * @IsGranted("ROLE_FOO")
     * @return JsonResponse
     */
    public function deleteAction(Request $request): JsonResponse
    {
        $post_id = $request->get('id');
        $post = $this->em->getRepository(Post::class)->find($post_id);

        $data = $this->serializer->serialize($post, JsonEncoder::FORMAT);

        $this->em->remove($post);
        $this->em->flush();

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
