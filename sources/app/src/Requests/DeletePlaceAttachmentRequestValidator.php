<?php declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeletePlaceAttachmentRequestValidator extends AbstractRequestValidator
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
            'attachment_id' => [new Assert\NotBlank]
        ]);

        return $this->proceed($this->validator->validate($input, $this->constraints));
    }
}
