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
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/auth', name: 'auth_')]
class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em,
     private UserPasswordHasherInterface $ph, private JWTTokenManagerInterface $jwtManager, 
     private AuthenticationSuccessHandler $authHandler, private MailerInterface $mailer){
       

    }
    #[Route('/registration', name: 'app_registration')]
    public function index(Request $request): Response 
    {
        //$authenticationSuccessHandler = $this->container->get('lexik_jwt_authentication.handler.authentication_success');
        //$jwtManager = $this->container->get('lexik_jwt_authentication.handler.authentication_success');
        $decoded = json_decode($request->getContent());
        $username = $decoded->username;
        $plaintextPassword = $decoded->password;
        $roles = $decoded->roles;
  
        $user = new User();
        $hashedPassword = $this->ph->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setUsername($username);
        $user->setRoles($roles);
        $this->em->persist($user);
        $this->em->flush();
        
            $email = (new Email())
                ->from('noreply@maajabutalent.com')
                ->to('sheltonsflash@gmail.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');
    
            $rs = $this->mailer->send($email);
    
            // ...
        
        $auth = $this->authHandler->handleAuthenticationSuccess($user);

        //$token = $this->jwtManager->create($user);
        $authContent = json_decode($auth->getContent());
  
        return $this->json(['message_en' => 'Registered Successfully',
        'message_fr' => 'Enregistré avec succès',
        'uid'=>$user->getId(),
        //'token'=>$token
        "token"=>$authContent->token,
        "refresh_token"=>$authContent->refresh_token,
        "mail"=>$rs
    ]);
    }
}