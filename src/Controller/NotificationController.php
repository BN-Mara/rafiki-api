<?php

namespace App\Controller;

use App\Entity\UserData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationController extends AbstractController
{
    

    public function __construct(private HttpClientInterface $client, private EntityManagerInterface $em){
       

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
        
        $users = $this->em->getRepository(UserData::class)->findAll();
        $devices = array();
        if ($users) {
            # code...
            foreach ($users as $key => $value) {
            array_push($devices,$value->getDeviceToken());
            }
            return $this->send($devices,"my title","my body",null);
        }
        else {
            # code...
            return $this->json(['failed'=>1],400);
        }
        
        
        /*$response=$this->client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'=> 'key=AAAA74CDm8k:APA91bHNL1Hoi9UXIRpfRTW-8Oc36azqS58RmRFtMUcswrYo_IRhErI9S1SRR5NJKPnepCuSOvdLOhZv6vBfyeflG9KPo0HACncQjaXQ3xdMKhjnRX6n-j_-YDZgu3iL3xLxUfGoDfrj'
            ],
            
            'json'=>[
 
                "registration_ids"=>["cFnSWQxnRb-KTtdG5jO8El:APA91bFwQU2w1kmWuPJC5H-wci7xrGuPVoRXx4-MIb6rr7PWm28id_kQG7pTe5mvVqIO_GZcvzzpfiheDwv3F-Z2ULpUfNnPm8RSLK25yxOGOlF8OJADiy-jlFOfCNcsFrS7kaosU3Av","ceWqCGwkS42yXsi7D3m5L9:APA91bG8YNS5_7XTA1Gc8AMoSbMwXCoZ_YeDiAkAYIgAUAtaNnAMxKiPrp-Tn-ByBsGS2AEedQ3Wv306lmzyVd4y0I53S5TlMHf4pHuxP4iC29cwnwQU-63uc9cU33XI0NOsoltIUM1X"],
                "notification" => [
                    "body" => "Upload recomandation",
                    "title"=> "You need to upload pastor letter to be verified"
                ],
                "data" => [
                    "body" => "You need to upload pastor letter to be verified",
                    "title"=>"Upload recomandation",
                    "type" => "PASTOR_URL"
                ]
            ]
        ]);*/
        //return $this->json(['success'=>$response->getContent()],$response->getStatusCode());
    }

    
    private function send(array $registratio_ids, string $title, string $body, ?string $type):Response{
        $response=$this->client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'=> 'key=AAAA74CDm8k:APA91bHNL1Hoi9UXIRpfRTW-8Oc36azqS58RmRFtMUcswrYo_IRhErI9S1SRR5NJKPnepCuSOvdLOhZv6vBfyeflG9KPo0HACncQjaXQ3xdMKhjnRX6n-j_-YDZgu3iL3xLxUfGoDfrj'
            ],
            
            'json'=>[
 
                "registration_ids"=>$registratio_ids,
                "notification" => [
                    "body" => $body,
                    "title"=> $title
                ],
                "data" => [
                    "body" => $body,
                    "title"=>$title,
                    "type" => $type
                ]
            ]
        ]);

      return $this->json(['content'=>$response->getContent()],$response->getStatusCode());

    }

}
