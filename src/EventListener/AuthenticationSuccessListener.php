<?php

namespace App\EventListener;

use App\Entity\UserData;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }
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
    //$userData = $this->em->getRepository(UserData::class)->findOneBy(['uid'=>"15"]);

    $data['sub'] = $user->getId();
    $event->setData($data);
}
}