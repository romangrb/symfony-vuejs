<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener {
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();

        if (isset($data['token'])) $data['token'] = 'Bearer ' . $data['token'];

        $data['user'] = $event->getUser()->getAttributes();

        $event->setData($data);
    }
}


