<?php declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class UpdatePlaceRequestValidator extends AbstractRequestValidator
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
            'description' => [new Assert\Length(['min' => 0, 'max' => 255])],
            'lat' => [new Assert\Regex('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/')],
            'lng' => [new Assert\Regex('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/')]
        ]);

        return $this->proceed($this->validator->validate($input, $this->constraints));
    }
}
