<?php declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class RenderPlaceContentTemplateRequestValidator extends AbstractRequestValidator
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
            'place_id' => [new Assert\NotBlank, new Assert\Length(['min' => 1])],
            'content' => [new Assert\NotBlank]
        ]);

        return $this->proceed($this->validator->validate($input, $this->constraints));
    }
}
