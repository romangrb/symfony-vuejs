<?php

declare(strict_types=1);

namespace App\Requests;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;
use JsonSchema\Validator;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class RequestValidator
{

    /**
     * Request prop
     **/
    protected $request;

    /**
     * Constructor
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Requests
     * @return Validator
     */
    public function validate(): Validator
    {
        $validator = new Validator();

        $data = json_decode($this->request->getContent(), true);

        /** @var User $user */
        $validator->validate(
            $data, (object) [
            "type"=>"array",
            "properties"=>(object)[
                "processRefund"=>(object)[
                    "type"=>"boolean"
                ],
                "refundAmount"=>(object)[
                    "type"=>"boolean"
                ]
            ]
        ]);

        return $validator;
    }

}
