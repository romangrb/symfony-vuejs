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
            'place_id' => [new Assert\NotBlank],
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

        if (isset($input['name'])) {
            $validation_params['name'] = [new Assert\Length(['min' => 1, 'max' => 50]), new Assert\NotBlank];
        }

        $this->constraints = new Assert\Collection($validation_params);

        return $this->proceed($this->validator->validate($input, $this->constraints));
    }
}
