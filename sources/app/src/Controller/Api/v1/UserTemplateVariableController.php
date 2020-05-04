<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\TemplateVariable;
use App\Repository\TemplateVariableRepository;
use App\Requests\CreateTemplateVariableRequestValidator;
use App\Requests\UpdateTemplateVariableRequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

final class UserTemplateVariableController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    /** @var TemplateVariableRepository */
    private $repository;

    /**
     * Initiate class properties
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param TemplateVariableRepository $repository
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, TemplateVariableRepository $repository)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->repository = $repository;
    }

    /**
     * Create template variable
     *
     * @Route("/template/variable", name="createTemplateVariable", methods={"POST"})
     * @param Request $request
     * @param Security $security
     * @param CreateTemplateVariableRequestValidator $validatorRequest
     * @return JsonResponse
     */
    public function createTemplateVariable(Request $request, Security $security, CreateTemplateVariableRequestValidator $validatorRequest): JsonResponse
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $tag = $request->get('tag');
        $value = $request->get('value');

        $input = [
            'name' => $name,
            'description' => $description,
            'value' => $value,
            'tag' => $tag,
        ];

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) return $validatedRequest;

        $user = $security->getUser();

        $templateVariable = new TemplateVariable();
        $templateVariable->setName($name);
        $templateVariable->setTag($tag);
        $templateVariable->setValue($value);
        $templateVariable->setDescription($description);
        $templateVariable->setUser($user);
        $this->em->persist($templateVariable);
        $this->em->flush();
        $data = $this->serializer->serialize($templateVariable, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Update template variable
     *
     * @Route("/template/variable/{id}", name="updateTemplateVariable", methods={"PATCH"})
     * @param Request $request
     * @param Security $security
     * @param UpdateTemplateVariableRequestValidator $validatorRequest
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @IsGranted("ROLE_FOO")
     */
    public function updateTemplateVariable(Request $request, Security $security, UpdateTemplateVariableRequestValidator $validatorRequest): JsonResponse
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $tag = $request->get('tag');
        $value = $request->get('value');
        $template_variable_id = (int) $request->get('id');
        $user = $security->getUser();

        $input = [
            'name' => $name,
            'description' => $description,
            'value' => $value,
            'tag' => $tag,
        ];

        $validatedRequest = $validatorRequest->validate($input);

        if ($validatedRequest) return $validatedRequest;

        $templateVariable = $this->repository->findUserTemplateVariable($user->getId(), $template_variable_id);

        if (! $templateVariable) return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);

        if ($name) $templateVariable->setName($name);
        if ($description) $templateVariable->setDescription($description);
        if ($tag) $templateVariable->setTag($tag);
        if ($value) $templateVariable->setValue($value);

        $this->em->persist($templateVariable);
        $this->em->flush();

        $data = $this->serializer->serialize($templateVariable, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Get all TemplateVariables
     *
     * @Route("/template/variables", name="getTemplateVariables", methods={"GET"})
     * @param Security $security
     * @return JsonResponse
     */
    public function getTemplateVariables(Security $security): JsonResponse
    {
        $user = $security->getUser();

        $templateVariables = $this->repository->getUserTemplateVariables($user->getId());
        $data = $this->serializer->serialize($templateVariables, JsonEncoder::FORMAT);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Delete TemplateVariables
     *
     * @Route("/template/variable/{id}", name="deleteTemplateVariables", methods={"DELETE"})
     * @param Request $request
     * @param Security $security
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @IsGranted("ROLE_FOO")
     */
    public function deleteTemplateVariables(Request $request, Security $security): JsonResponse
    {
        $template_variable_id = (int) $request->get('id');
        $user = $security->getUser();
        $templateVariable = $this->repository->findUserTemplateVariable($user->getId(), $template_variable_id);

        if (! $templateVariable) return new JsonResponse('', Response::HTTP_NOT_FOUND, [], true);

        $this->em->remove($templateVariable);
        $this->em->flush();

        return new JsonResponse('', Response::HTTP_NO_CONTENT, [], true);
    }
}
