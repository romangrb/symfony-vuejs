<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Repository\PlaceRepository;
use App\Requests\ShowPlaceByLocationRequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PlaceContent;
use Symfony\Component\Filesystem\Filesystem;
use App\Transformers\ErrorExceptionTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class IndexController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PlaceRepository */
    private $repository;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * Initiate class properties
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param PlaceRepository $repository
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, PlaceRepository $repository)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->repository = $repository;
    }

    /**
     * Get place by location attr
     *
     * @Route("/place/@{lat},{lng}", name="getPlaceByLocations", methods={"GET"})
     * @param Request $request
     * @param ShowPlaceByLocationRequestValidator $validatorRequest
     * TODO make response html
     * @param LoggerInterface $logger
     * @return Response
     */
    public function getPlaceByLocations(Request $request, ShowPlaceByLocationRequestValidator $validatorRequest, LoggerInterface $logger)
    {
        $lat = (float) $request->get('lat');
        $lng = (float) $request->get('lng');

        $input = [
            'lat' => $lat,
            'lng' => $lng,
        ];

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) {
            return new Response('',Response::HTTP_NOT_FOUND);
        }

        $placeContent = $this->em->getRepository(PlaceContent::class)->getPlaceContentByLocation($lat, $lng);

        if (! $placeContent || ! $placeContent->getIsPublished()) {
            return new Response('',Response::HTTP_NOT_FOUND);
        }

        $data = [
            'gjs-css'  => '',
            'gjs-html' => ''
        ];

        try {
//            TODO:: move to one class to use on 2 routes
            $filesystem = new Filesystem();
            $path = $this->getParameter('kernel.project_dir') . "/var/places/{$placeContent->getPlace()->getId()}/";

            $file_name = $placeContent->getFileName();

            if ($file_name && $filesystem->exists("$path/" . $file_name)
            ) {
                $data['gjs-html'] = file_get_contents($path . $file_name);
            }
        } catch (\Exception $e) {
            $message = ErrorExceptionTransformer::transform($e);
            $logger->error(print_r($message, true));
            return new Response('',Response::HTTP_NOT_FOUND);
        }
// TODO merge to one file return HTML
        return new Response($data, Response::HTTP_OK, []);
    }
}
