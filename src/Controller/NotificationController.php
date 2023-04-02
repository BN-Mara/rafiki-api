<?php

namespace App\Controller;

use App\Entity\UserData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationController extends AbstractController
{
    private $notifyer;

    public function __construct(HttpClientInterface $notifyer){
        $this->$notifyer = $notifyer;

    }
    #[Route('/notification', name: 'app_notification')]
    public function index(): Response
    {
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
        ]);
    }

    #[Route('/notification/all', name: 'app_notification_all')]
    public function notifyAll(): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $users = $manager->getRepository(UserData::class)->findAll();
        $this->notifyer->request('POST', 'https://...', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'=> 'bearer '
            ],
            'body'=>
        ])
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
        ]);
    }
}
