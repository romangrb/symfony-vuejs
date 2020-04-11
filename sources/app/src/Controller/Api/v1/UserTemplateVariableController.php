<?php declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Entity\TemplateVariable;
use App\Repository\TemplateVariableRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Security;

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
     * @Rest\Post("/template/variable", name="createTemplateVariable")
     * @param Request $request
     * @param Security $security
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createTemplateVariable(Request $request, Security $security, ValidatorInterface $validator): JsonResponse
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

        $constraints = new Assert\Collection([
            'name' => [new Assert\Length(['min' => 3, 'max' => 50]), new Assert\NotBlank],
            'description' => [new Assert\Length(['min' => 0, 'max' => 255])],
            'value' => [new Assert\Length(['min' => 0, 'max' => 255]), new Assert\NotBlank],
            'tag' => [
                new Assert\Unique(['payload' => 'tag', 'groups' => 'string']),
                new Assert\Regex(['pattern' => '/^\S+\w{2,32}$/', 'message' => 'The tag should not contain space or tab and contains alphanumeric character including _']),
                new Assert\Length(['min' => 2, 'max' => 255])
            ],
        ]);

        $user = $security->getUser();

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
     * @Rest\Patch("/template/variable/{id}", name="updateTemplateVariable")
     * @param Request $request
     * @param Security $security
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @IsGranted("ROLE_FOO")
     */
    public function updateTemplateVariable(Request $request, Security $security, ValidatorInterface $validator): JsonResponse
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

        $constraints = new Assert\Collection([
            'name' => [new Assert\Length(['min' => 3, 'max' => 50]), new Assert\Optional()],
            'description' => [new Assert\Length(['min' => 0, 'max' => 255])],
            'value' => [new Assert\Length(['min' => 0, 'max' => 255]), new Assert\NotBlank],
            'tag' => [
                new Assert\Unique(['payload' => 'tag', 'groups' => 'string']),
                new Assert\Regex(['pattern' => '/^\S+\w{2,32}$/', 'message' => 'The tag should not contain space or tab and contains alphanumeric character including _']),
                new Assert\Length(['min' => 2, 'max' => 255])
            ],
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
     * @Rest\Get("/template/variables", name="getTemplateVariables")
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
     * @Rest\Delete("/template/variable/{id}", name="deleteTemplateVariables")
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
