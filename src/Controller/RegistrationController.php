<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[Route('/auth', name: 'auth_')]
class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em,
     private UserPasswordHasherInterface $ph, private JWTTokenManagerInterface $jwtManager){
       

    }
    #[Route('/registration', name: 'app_registration')]
    public function index(Request $request): Response
    {
        $authenticationSuccessHandler = $this->container->get('lexik_jwt_authentication.handler.authentication_success');
        //$jwtManager = $this->container->get('lexik_jwt_authentication.handler.authentication_success');
        $decoded = json_decode($request->getContent());
        $username = $decoded->username;
        $plaintextPassword = $decoded->password;
  
        $user = new User();
        $hashedPassword = $this->ph->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setUsername($username);
        $this->em->persist($user);
        $this->em->flush();

        $token = $this->jwtManager->create($user);
        
  
        return $this->json(['message_en' => 'Registered Successfully',
        'message_fr' => 'Enregistré avec succès',
        'uid'=>$user->getId(),
        'token'=>$token
    ]);
    }
}
