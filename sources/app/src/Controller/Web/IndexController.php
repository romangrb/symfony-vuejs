<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Repository\PlaceContentRepository;
use App\Repository\PlaceRepository;
use App\Requests\ShowPlaceByLocationRequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use App\Transformers\ErrorExceptionTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class IndexController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PlaceRepository */
    private $repository;

    /** @var SerializerInterface */
    private $serializer;

    /** @var Environment */
    private $twig;

    /**
     * Initiate class properties
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param PlaceContentRepository $repository
     * @param Environment $twig
     */
    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        PlaceContentRepository $repository,
        Environment $twig
    ) {
        $this->em = $em;
        $this->twig = $twig;
        $this->serializer = $serializer;
        $this->repository = $repository;
    }

    /**
     * Get place by location attr
     *
     * @Route("/place/@{lat},{lng}", name="getPlaceByLocations", methods={"GET"})
     * @param Request $request
     * @param ShowPlaceByLocationRequestValidator $validatorRequest
     * @param LoggerInterface $logger
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getPlaceByLocations(
        Request $request,
        ShowPlaceByLocationRequestValidator $validatorRequest,
        LoggerInterface $logger): Response
    {
        $lat = (float) $request->get('lat');
        $lng = (float) $request->get('lng');

        $input = [
            'lat' => $lat,
            'lng' => $lng,
        ];

        if ($validatorRequest->validate($input)) {
            return new Response('',Response::HTTP_NOT_FOUND);
        }

        $placeContent = $this->repository->getPlaceContentByLocation($lat, $lng);

        if (! $placeContent || ! $placeContent->getIsPublished()) {
            return new Response('',Response::HTTP_NOT_FOUND);
        }

        $content = '';

        try {
            $filesystem = new Filesystem();
            $path = $this->getParameter('kernel.project_dir') . "/var/places/{$placeContent->getPlace()->getId()}/";

            $file_name = $placeContent->getFileName();

            if ($file_name && $filesystem->exists("{$path}/{$file_name}")) {
                $content = file_get_contents($path . $file_name);
            }

        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $logger->error(print_r($message, true));
            return new Response('',Response::HTTP_NOT_FOUND);
        }

        $contents = $this->twig->render('place.html.twig', [
            'title' => $placeContent->getPlace()->getName(),
            'content' => $content,
        ]);

        return new Response($contents);
    }
}
