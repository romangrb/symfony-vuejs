<?php declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractRequestValidator
{
    /**
     * Request prop
     **/
    protected $validator;

    /**
     * Request prop that will be validated
     **/
    protected $input;

    /**
     * Validation Rules
     **/
    protected $constraints;

    /**
     * Constructor
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Requests
     *
     * @param array $input
     * @return JsonResponse
     */
    abstract public function validate(array $input): ?JsonResponse;

    /**
     * Requests
     *
     * @param $violations
     * @return JsonResponse
     */
    protected function proceed($violations): ?JsonResponse
    {
        if (! count($violations) > 0) return null;

        $accessor = PropertyAccess::createPropertyAccessor();
        $errorMessages = [];
        foreach ($violations as $violation) {
            $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
        }

        return new JsonResponse($errorMessages, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
