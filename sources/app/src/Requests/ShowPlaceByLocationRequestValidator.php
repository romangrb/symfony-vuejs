<?php declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShowPlaceByLocationRequestValidator extends AbstractRequestValidator
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
            'lat' => [new Assert\Regex('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/')],
            'lng' => [new Assert\Regex('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/')]
        ]);

        return $this->proceed($this->validator->validate($input, $this->constraints));
    }
}
