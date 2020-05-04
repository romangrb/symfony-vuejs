<?php

namespace App\Controller\Api\v1;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    public function register(
        Request $request,
        JWTTokenManagerInterface $JWTManager,
        UserPasswordEncoderInterface $encoder,
        ValidatorInterface $validator
    ) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);

        $username = $request->get('username');
        $password = $request->get('password');
        $email = $request->get('email');

        $constraint = new Assert\Collection([
            'username' => new Assert\NotBlank(),
            'password' => [
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 6))
            ],
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email()
            ]
        ]);

        $violations = $validator->validate(compact('username', 'password', 'email'), $constraint);
        if (count($violations) > 0) {
            return $this->showViolations($violations);
        }

        $user = new User();

        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setUsername($username);

        $violations = $validator->validate($user);
        if (count($violations) > 0) {
            return $this->showViolations($violations);
        }

        try {
            $em->persist($user);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500);
        }

        return new JsonResponse([
            "token" => $JWTManager->create($user),
            "user" => $user->getAttributes()
        ], 200);
    }

    /**
     * @param $violations
     * @return JsonResponse
     */
    protected function showViolations($violations)
    {
        $errorMessages = [];

        foreach ($violations as $violation) {
            $field = trim($violation->getPropertyPath(), '[]');
            $errorMessages[$field] = $violation->getMessage();
        }

        return  new JsonResponse($errorMessages, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    /**
     * @param Request $request
     */
    public function test(Request $request)
    {
        dd($this->getUser());
    }
}