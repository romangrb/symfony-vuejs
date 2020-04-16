<?php declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class AttachPlaceContentTemplateRequestValidator extends AbstractRequestValidator
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
            'content' => [new Assert\Length(['min' => 1]), new Assert\NotBlank],
            'place_id' => [new Assert\NotBlank],
            'description' => [new Assert\Length(['min' => 0, 'max' => 255])],
            'is_published' => [new Assert\Length(['min' => 0, 'max' => 1])]
        ]);

        return $this->proceed($this->validator->validate($input, $this->constraints));
    }
}
