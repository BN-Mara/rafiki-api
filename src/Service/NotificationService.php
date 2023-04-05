<?php

namespace App\Service;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationService{
    

    public function __construct(private HttpClientInterface $client, private EntityManagerInterface $em)
    {
        
    }

    public function notify(array $ids, Notification $notif):Response{
        $this->em->persist($notif);
        $this->em->flush();
        return $this->send($ids,$notif);
    }
    
    private function send(array $registratio_ids, Notification $notif):Response{
        $response=$this->client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'=> 'key=AAAA74CDm8k:APA91bHNL1Hoi9UXIRpfRTW-8Oc36azqS58RmRFtMUcswrYo_IRhErI9S1SRR5NJKPnepCuSOvdLOhZv6vBfyeflG9KPo0HACncQjaXQ3xdMKhjnRX6n-j_-YDZgu3iL3xLxUfGoDfrj'
            ],
            
            'json'=>[
 
                "registration_ids"=>$registratio_ids,
                "notification" => [
                    "body" => $notif->getBody(),
                    "title"=> $notif->getTitle()
                ],
                "data" => [
                    "body" => $notif->getBody(),
                    "title"=>$notif->getTitle(),
                    "type" => $notif->getType(),
                ]
            ]
        ]);
        if($response->getStatusCode() == 200){
            $notif->setIsSent(true);
            $notif->setSentTime(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));
            $this->em->flush();
        }

    return new JsonResponse(['content'=>$response->getContent()/*,"ids"=>$registratio_ids*/],$response->getStatusCode());

    }

}