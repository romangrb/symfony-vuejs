<?php declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class UpdateTemplateVariableRequestValidator extends AbstractRequestValidator
{
    /**
     * Requests
     *
     * @param array $input
     * @return JsonResponse
     */
    public function validate(array $input): ?JsonResponse
    {
        $this->constraints = new Assert\Collection([
            'name' => [new Assert\Length(['min' => 3, 'max' => 50]), new Assert\Optional()],
            'description' => [new Assert\Length(['min' => 0, 'max' => 255])],
            'value' => [new Assert\Length(['min' => 0, 'max' => 255]), new Assert\NotBlank],
            'tag' => [
                new Assert\Unique(['payload' => 'tag', 'groups' => 'string']),
                new Assert\Regex(['pattern' => '/^\S+\w{2,32}$/', 'message' => 'The tag should not contain space or tab and contains alphanumeric character including _']),
                new Assert\Length(['min' => 2, 'max' => 255])
            ],
        ]);

        return $this->proceed($this->validator->validate($input, $this->constraints));
    }
}
