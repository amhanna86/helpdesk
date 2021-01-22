<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }
        $data['user'] = array(
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'userName' => $user->getFullUsername(),
            'roles' => $user->getRoles(),
            'type' => $user->getUserType()->getType(),
            'expirationDate' => time() + 3600
        );
        $event->setData($data);
    }
}