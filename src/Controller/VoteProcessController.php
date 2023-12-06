<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\NewsLetterSubscriber;
use App\Entity\Payment;
use App\Entity\Prime;
use App\Entity\UserData;
use App\Entity\Vote;
use App\Entity\VoteMode;
use App\Helper\PaymentUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Stripe\Checkout\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Stripe\Stripe;

class VoteProcessController extends AbstractController
{
    public $container;
    private $payum;
    public function __construct(private EntityManagerInterface $em, 
    private PaymentUrl $payUrl, ContainerInterface $container)
    {
        $this->container = $container;
        
    }
    #[Route('/vote/process', name: 'app_vote_process')]
    public function index(): Response
    {
        return $this->render('vote_process/index.html.twig', [
            'controller_name' => 'VoteProcessController',
        ]);
    }
    #[Route('/vote/process/start', name: 'app_vote_process_start')]
    public function process(Request $request): Response
    {
       if($request->query->has('username')){
            $session = $request->getSession()->set('username',$request->query->get('username'));
        }
        if($request->getMethod() === 'POST'){
        //$content = json_decode($request->getContent());
   
        $voteModeId = $request->request->get('voteModeId');
        $artistId = $request->request->get('artistId');
       // $type = $request->request->get('type');
        
        //$voteMode = new VoteMode();
        //die(var_dump($content));
        $voteMode = $this->em->getRepository(VoteMode::class)->find($voteModeId);
        
        if(! $voteMode){
            return new Response("Vote type not found");
        }
        if($request->request->has('stripe')){
            if($voteMode->getPrice() < 0.5){
                $this->addFlash(
                    'danger',
                    'Le montant total dû de la session de paiement doit s\'élever à au moins 0,50 USD.'
                );
                return $this->redirectToRoute('app_vote_process_start');


            }
            
        }
        $artist = $this->em->getRepository(Artist::class)->find($artistId);
        if(! $artist){
            return new Response("Artist not found");
        }
        $currency = $request->request->get('currency');
        $competition = $artist->getCompetition();
        $prime = $this->em->getRepository(Prime::class)->findOneBy(['competitionId'=>$competition->getId(),'isActive'=>true]);
        if(! $prime){
            return new Response("Prime not found");
        }
        $session = $request->getSession();

        $vote = new Vote();
        $vote->setArtist($artist);
        $vote->setNumberOfVote($voteMode->getNumberOfVote());
        $vote->setPrime($prime);
        $vote->setIsPayed(false);
        $this->em->persist($vote);

        
        $payment =  new Payment();

        $payment->setAmount($voteMode->getPrice());
        $payment->setCurrency($currency);
        $payment->setVote($vote);
        $payment->setStatus('PENDING');
        if($session->has('username')){
            $user = $this->em->getRepository(UserData::class)->findOneBy(['email'=>$session->get('username')]);
            if($user){
                $payment->setEmail($user->getEmail());
                $payment->setPhone($user->getPone());
                $payment->setUsername($user->getName());
            }
        }else{
            $payment->setEmail("");
            $payment->setPhone("");
            $payment->setUsername("");
        }
        
        //$r = substr($competition->getCode(),0,2).''.$prime->getId();
        
        
        if($request->request->has('illico'))
        {
            $r = $this->generateReference('IL',$prime->getId());
            $session->set('paymode','ILLICO');
        }else if($request->request->has('stripe')){
            $r = $this->generateReference('ST',$prime->getId());
            $session->set('paymode','STRIPE');
        }else if($request->request->has('araka')){
            $r = $this->generateReference('AR',$prime->getId());
            $session->set('paymode','ARAKA');
        }else{
            $r = $this->generateReference('MA',$prime->getId());
            $session->set('paymode','MAXICASH');
        }
        $payment->setReference($r);
        $this->em->persist($payment);

        $this->em->flush();

        
        $session->set('reference',$payment->getReference());

        if($request->request->has('illico'))
        {
            return $this->redirectToRoute('app_illico',
            [
            "reference"=>$payment->getReference(),
            "amount"=>$payment->getAmount(),
            "currency"=>$payment->getCurrency()]);
        }
        if($request->request->has('araka')){
            
                $url = 'https://araka-merchant-uat.azurewebsites.net/payment/9DE6E3AE-D1A2-49CF-B62C-590BCA87E4DE?transactionReference='.$payment->getReference().'&currency=USD&amount='.$payment->getAmount().'&merchant=b255c76a-41cc-4398-bfd2-b2f9e3238c2d';
 
            return $this->redirect($url);

        }
        if($request->request->has('stripe')){
          
            
            Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
            header('Content-Type: application/json');

            $YOUR_DOMAIN = 'http://localhost:4242';

            $checkout_session = Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                      'name' => $voteMode->getDescription(),
                    ],
                    'unit_amount' => ($payment->getAmount() * 100),
                  ],
                  'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $request->getSchemeAndHttpHost() . '/vote/process/success',
            'cancel_url' => $request->getSchemeAndHttpHost() . '/vote/process/fail',
            ]);
            return $this->redirect($checkout_session->url);
        
            
        }
        
        return $this->redirect($this->payUrl->paymentUrl($payment, $request->getSchemeAndHttpHost()));
    

        }

        $artists = $this->em->getRepository(Artist::class)->findBy([],['numero' => 'ASC']);
        $vmodes = $this->em->getRepository(VoteMode::class)->findAll();
        $artistArray = array_chunk($artists,4);
        $total_text = count($artists)." Artist";
        if(count($artists) > 1){
            $total_text = count($artists)." Artists"; 
        }
        

        return $this->render('vote_process/votelist.html.twig', [
            'controller_name' => 'VoteProcessController',
            'artistArray'=>$artistArray,
            'voteModes'=>$vmodes,
            'artists'=>$artists,
            "total"=>$total_text
        ]);
    }
    #[Route('/vote/process/restart', name: 'app_vote_process_restart')]
    public function paymentRestart(Request $request):Response{

        if($request->getMethod() === 'POST'){

        $reference = $request->request->get('reference');
        $payment2 = $this->em->getRepository(Payment::class)->findOneBy(['reference'=>$reference]);
            if (!$payment2) {
                # code...
                return $this->redirectToRoute('app_vote_process_start');
                //return new Response("payment for Refecenfe not found");
            }


        $session = $request->getSession();

        $session->set('reference',$reference);
        if($payment2->getStatus() != 'PAYED'){
            $s = substr($reference, -3);

            $v = $payment2->getVote();
            

            $vote = new Vote();
        $vote->setArtist($v->getArtist());
        $vote->setNumberOfVote($v->getNumberOfVote());
        $vote->setPrime($v->getPrime());
        $vote->setIsPayed(false);
        $this->em->persist($vote);

        
        $payment =  new Payment();

        $payment->setAmount($payment2->getAmount());
        $payment->setCurrency($payment2->getCurrency());
        $payment->setVote($vote);
        $payment->setStatus('PENDING');
        $payment->setEmail($payment2->getEmail());
        $payment->setPhone($payment2->getPhone());
        $payment->setUsername($payment2->getUsername());
        //$r = substr($competition->getCode(),0,2).''.$prime->getId();
       
        if($request->request->get('paymode') == "ILLICO")
        {
            $r = $this->generateReference('IL',$v->getPrime()->getId());
        }else if($request->request->get('paymode') == 'STRIPE'){
            $r = $this->generateReference('ST',$v->getPrime()->getId());
        }else{
            $r = $this->generateReference('MA',$v->getPrime()->getId());
        }
        $payment->setReference($r);
        $this->em->persist($payment);

        $this->em->flush();

        $session = $request->getSession();
        $session->set('reference',$payment->getReference());

        if($request->request->get('paymode') == 'ILLICO')
        {
            return $this->redirectToRoute('app_illico',
            ["reference"=>$payment->getReference(),
            "amount"=>$payment->getAmount(),
            "currency"=>$payment->getCurrency()]);
        }
        if($request->request->get('paymode') =='STRIPE'){
            
            Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
            header('Content-Type: application/json');

            $checkout_session = Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                      'name' => ''.$vote->$v->getNumberOfVote().'Votes à '.$payment2->getAmount().' USD',
                    ],
                    'unit_amount' => ($payment->getAmount() * 100),
                  ],
                  'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $request->getSchemeAndHttpHost() . '/vote/process/success',
            'cancel_url' => $request->getSchemeAndHttpHost() . '/vote/process/fail',
            ]);
            return $this->redirect($checkout_session->url);
        }

            return $this->redirect($this->payUrl->paymentUrl($payment,$request->getSchemeAndHttpHost()));

        }else{
           return $this->redirectToRoute('app_vote_process_start');
        }
        
        
        }
        return $this->redirectToRoute('app_vote_process_start');
        

    }


    #[Route('/vote/process/success', name: 'app_vote_process_success')]
    public function paymentSuccess(Request $request): Response
    {
        $session = $request->getSession();
        if(!$session->has('reference')){
            return $this->redirectToRoute('app_vote_process_start');
        }
       
            $ref = $session->get('reference');
            //$payment = new Payment();
            $payment = $this->em->getRepository(Payment::class)->findOneBy(['reference'=>$ref]);
            if (!$payment) {
                # code...
                return new Response("payment for Refecenfe not found");
            }
            $vote = $payment->getVote();
            $vote->setIsPayed(true);
            $payment->setStatus('PAID');
            if($session->has('syetemReference')){
                $payment->setGatewayReference($session->get('syetemReference'));
            }
            $vote->setPayment($payment);
            $this->em->flush();
            $session->remove('reference');
            $session->remove('paymode');
            $session->remove('syetemReference');

           
          
        
        return $this->render('vote_process/success.html.twig', [
            'controller_name' => 'VoteProcessController',
            'artist'=>$vote->getArtist(),
            'vote'=>$vote,
            'reference'=>$payment->getReference()
        ]);

    }

    

    #[Route('/vote/process/fail', name: 'app_vote_process_fail')]
    public function paymentFailed(Request $request):Response{
      
        $session = $request->getSession();
        if(!$session->has('reference')){
            return $this->redirectToRoute('app_vote_process_start');
        }
       
       
            $ref = $session->get('reference');
            //$payment = new Payment();
            $payment = $this->em->getRepository(Payment::class)->findOneBy(['reference'=>$ref]);
            if (!$payment) {
                # code...
                return new Response("payment for Refecenfe not found");
            }
            $vote = $payment->getVote();
            $vote->setIsPayed(false);
            $payment->setStatus('FAILED');
            if($session->has('syetemReference')){
                $payment->setGatewayReference($session->get('syetemReference'));
            }
            $vote->setPayment($payment);
            $this->em->flush();
            $paymode = $session->get('paymode');
            $session->remove('reference');
            $session->remove('paymode');
            $session->remove('syetemReference');
            
           
        
            return $this->render('vote_process/failed.html.twig', [
                'status' => 'failed',
                'artist'=>$vote->getArtist(),
                'vote'=>$vote,
                'reference'=>$payment->getReference(),
                'paymode'=>$paymode
            ]);
    
    }
    #[Route('/vote/process/cancel', name: 'app_vote_process_cancel')]
    public function paymentCanceled(Request $request):Response{
        $session = $request->getSession();
        if(!$session->has('reference')){
            return $this->redirectToRoute('app_vote_process_start');
        }

            $ref = $session->get('reference');
            //$payment = new Payment();
            $payment = $this->em->getRepository(Payment::class)->findOneBy(['reference'=>$ref]);
            if (!$payment) {
                # code...
                return new Response("payment for Refecenfe not found");
            }
            $vote = $payment->getVote();
            $vote->setIsPayed(false);
            $payment->setStatus('CANCELED');
            if($session->has('syetemReference')){
                $payment->setGatewayReference($session->get('syetemReference'));
            }
            $vote->setPayment($payment);
            $this->em->flush();
            $paymode = $session->get('paymode');
            $session->remove('reference');
            $session->remove('paymode');
            $session->remove('syetemReference');
           
        return $this->render('vote_process/failed.html.twig', [
            'status' => 'canceled',
            'artist'=>$vote->getArtist(),
            'vote'=>$vote,
            'reference'=>$payment->getReference(),
            'paymode'=>$paymode
        ]);

    }
    #[Route('/vote/process/araka-return', name: 'app_vote_process_araka-return')]
    public function arakaReturn(Request $request): Response
    {
        $session = $request->getSession();
        if(!$session->has('reference')){
            return $this->redirectToRoute('app_vote_process_start');
        }
        if($request->query->has('syetemReference')){
            $session->set('syetemReference',$request->query->get('syetemReference'));
        }
        
        if($request->query->has('transactionStatus') && $request->query->get('transactionStatus') === 'SUCCESS'){
            return $this->redirectToRoute('app_vote_process_success');
        }else{
            return $this->redirectToRoute('app_vote_process_fail');
        }
       
    }

    #[Route('/vote/process/notify', name: 'app_vote_process_notify')]
    public function paymentNotify(Request $request):Response{
       
        return $this->json(["success"=>true,"message"=>"Ok"]);

    }

    #[Route('/vote/process/search', name: 'app_vote_process_search')]
    public function search(Request $request):Response{
        $sh = $request->request->get("search");

        $artists = $this->em->getRepository(Artist::class)->findBySearchTerm($sh);
        $vmodes = $this->em->getRepository(VoteMode::class)->findAll();
        //$artistArray = array_chunk($artists,4);
        $total_text = count($artists)." Artist";
        if(count($artists) > 1){
            $total_text = count($artists)." Artists";
        }
       
       $template = $this->render('vote_process/_list.html.twig',["artists"=>$artists,"voteModes"=>$vmodes])->getContent();
        return $this->json(["list"=>$template,"total"=>$total_text, ]);
    }

    
    #[Route('/vote/process/newsletter', name: 'app_vote_process_newsletter')]
    public function subscribe(Request $request):Response{
        $sh = $request->request->get("email");
        $news = new NewsLetterSubscriber();
        $news->setEmail($sh);
        $this->em->persist($news);
        $this->em->flush();
        return $this->json(["success"=>true,"message"=>"Ok"]);

    }
    private function generateReference($payMode,$prime):string{
       
        $old = strtotime(date('Y-m-d h:i:s', strtotime('1970-01-01 10:00:00')));
        $now = strtotime(date('Y-m-d h:i:s'));
        $dif = $now - $old;
        $dif = $dif.$prime.$payMode;
        if($this->em->getRepository(Payment::class)->findOneBy(['reference'=>$dif])){
           return $this->generateReference($payMode,$prime);
        }
        return $dif;
    

    }
}
