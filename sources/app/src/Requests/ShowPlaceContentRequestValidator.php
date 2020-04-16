<?php declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShowPlaceContentRequestValidator extends AbstractRequestValidator
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
            'name' => [new Assert\Length(['min' => 3, 'max' => 255]), new Assert\NotBlank],
            'description' => [new Assert\Length(['min' => 0, 'max' => 255])]
        ]);

        return $this->proceed($this->validator->validate($input, $this->constraints));
    }
}
