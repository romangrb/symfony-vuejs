<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class PostController extends AbstractController
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
     * @Route("/posts", name="createPost", methods={"POST"})
     * @param Request $request
     * @IsGranted("ROLE_FOO")
     * @return JsonResponse
     */
    public function createAction(Request $request): JsonResponse
    {
        $message = $request->get('message');

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
     * @Route("/post/{id}", name="createPost", methods={"PUT"})
     * @param Request $request
     * @IsGranted("ROLE_FOO")
     * @return JsonResponse
     */
    public function updateAction(Request $request): JsonResponse
    {
        $message = $request->get('message');
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
     * @Route("/posts", name="findAllPosts", methods={"GET"})
     */
    public function findAllAction(): JsonResponse
    {
        $posts = $this->em->getRepository(Post::class)->findBy([], ['id' => 'DESC']);
        $data = $this->serializer->serialize($posts, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/post/{id}", name="deletePost", methods={"DELETE"})
     *
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
