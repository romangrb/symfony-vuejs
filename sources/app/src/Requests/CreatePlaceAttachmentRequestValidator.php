<?php declare(strict_types=1);

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreatePlaceAttachmentRequestValidator extends AbstractRequestValidator
{
    /**
     * Requests
     *
     * @param array $input
     * @return JsonResponse
     */
    public function validate(array $input): ?JsonResponse
    {
        $validation_params = [
            'attachment' => [new Assert\NotBlank, new Assert\File([
                'maxSize' => '10M',
                'mimeTypes' => [
                    'image/gif',
                    'image/jpg',
                    'image/jpeg',
                ],
                'mimeTypesMessage' => 'Please upload a valid image format gif, jpg, jpeg',
            ])],
        ];

        $this->constraints = new Assert\Collection($validation_params);

        return $this->proceed($this->validator->validate($input, $this->constraints));
    }
}
