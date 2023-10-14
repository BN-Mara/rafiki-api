<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\ResponseInterface;
use App\Entity\Payment;

class IllicoController extends AbstractController
{
    public function __construct(private HttpClientInterface $client, private EntityManagerInterface $em)
    {
        
    }
    #[Route('/illico', name: 'app_illico')]
    public function index(Request $request): Response
    {  
        $session = $request->getSession();
        if(!$session->has('reference')){
            return $this->redirectToRoute('app_vote_process_start');
        }
        //$routeParameters = $request->attributes->get('_route_params');
        

        return $this->render('illico/index.html.twig', [
            'controller_name' => 'IllicoController',
            'reference'=>$request->get('reference'),
            'amount'=>$request->get('amount'),
            'currency'=>$request->get('currency')
        ]);
    }

    #[Route('/illico/pay', name: 'app_illico_pay')]
    public function handlePayment(Request $request): Response{
        $num = $request->request->get("phone");
        if(strlen($num)  < 9 && !is_numeric($num)){
            return $this->json(["status"=>false,"message"=>"Invalid phone number.".$num]);

        }
        $numFormated =  "00243".substr($num,-9);
        $session = $request->getSession();
        
        $ref = $session->get('reference');
        //$payment = new Payment();
        $payment = $this->em->getRepository(Payment::class)->findOneBy(['reference'=>$ref]);
        if (!$payment) {
            # code...
            return $this->json(["status"=>false,"message"=>"Invalid payment details."]);
        }
        $response=$this->client->request('POST', 'https://test.new.rawbankillico.com:4003/RAWAPIGateway/ecommerce/payment', [
            'headers' => [
                
                'LogInName'=>'a5169891f7424defec80033e2c4264004716e4846b6929caea8f431c7568d604',
                'Content-Type' => 'application/json',
                'LoginPass'=>'22cf830393691407806b22424dd66354e543c0e34f8161f00df1e74fa0a61e2b',
                /*'auth_basic' => ['delta', '123456'],*/
                /*'Authoization'=>'Basic ZGVsdGE6MTIzNDU2'*/
                'Authorization'=> 'Basic ZGVsdGE6MTIzNDU2'
            ],
            
            'json'=>[
 
                "mobilenumber"=> $numFormated,
	            "trancurrency"=>$payment->getCurrency(),
	            "amounttransaction"=>$payment->getAmount(),
	            "merchantid"=>"merch0000000000001042",
	            "invoiceid"=>$payment->getReference(),
	            "terminalid"=>"123456789012",
	            "encryptkey"=>"NozZSGL660ZZM8u4kUTV4CfgSy3G7wpFDQ0vCOhLWLpmnkNLkGia6mn7J2j2f4CJ/RDKF0ICxN7mBD9ciURYWj97KT2LYBoaPJVJs3hv5s5SGYoOw4fcAigt7+0nQiza",
	            "securityparams"=>[
		            "gpslatitude"=>"24.864190",
		            "gpslongitude"=>"67.090420"
                ]
                
            ]
        ]);
        if($response->getStatusCode() == 200){
            //$ri = new ResponseInterface();
            $rep = json_decode($response->getContent());

            //$notif->setSentTime(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));
            if($rep->respcode = "00"){
            $refNumber = $rep->referencenumber;
            $session->set('illico_ref',$refNumber);

            return $this->json(["status"=>true,"message"=>"payment ok", "data"=>$rep]);
            }else{
                return $this->json(["status"=>false,"message"=>"payment ko"]);
            }
        }

        return $this->json(["status"=>true,"message"=>"$numFormated","response"=>$response->getStatusCode()]);
    }

    #[Route('/illico/pay/otp', name: 'app_illico_pay_otp')]
    public function handlePaymentOTP(Request $request): Response{
        if($request->getMethod() === 'POST'){
        $otp = $request->request->get("otp");
        $session = $request->getSession();
        if(!$session->has('illico_ref')){
            return $this->json(["status"=>false,"message"=>"Invalid operation", "redirect"=>""],302);
        }
        $ref_illico = $session->get('illico_ref');
        $response=$this->client->request('GET', 'https://test.new.rawbankillico.com:4003/RAWAPIGateway/ecommerce/payment/'.$otp.'/'.$ref_illico, [
            'headers' => [
                
                'LogInName'=>'a5169891f7424defec80033e2c4264004716e4846b6929caea8f431c7568d604',
                'Content-Type' => 'application/json',
                'LoginPass'=>'22cf830393691407806b22424dd66354e543c0e34f8161f00df1e74fa0a61e2b',
                /*'auth_basic' => ['delta', '123456'],*/
                /*'Authoization'=>'Basic ZGVsdGE6MTIzNDU2'*/
                'Authorization'=> 'Basic ZGVsdGE6MTIzNDU2'
            ],
        ]
        );
        $content = json_decode($response->getContent());
        if($response->getStatusCode() == 200 && $content->respcode == "00"){
            return $this->redirectToRoute('app_vote_process_success');
        }else{
            //$this->addFlash("danger","Invalid OTP, please try again.");
            //return $this->redirectToRoute('app_illico');
            return $this->redirectToRoute('app_vote_process_fail');
        }
    }
    return new Response("",204);


    }
}
