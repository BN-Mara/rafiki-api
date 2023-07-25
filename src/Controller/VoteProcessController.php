<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Payment;
use App\Entity\Prime;
use App\Entity\Vote;
use App\Entity\VoteMode;
use App\Helper\PaymentUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VoteProcessController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private PaymentUrl $payUrl)
    {
        
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
        if($request->getMethod() === 'POST'){
        //$content = json_decode($request->getContent());
        $voteModeId = $request->request->get('voteModeId');
        $artistId = $request->request->get('artistId');
        
        //$voteMode = new VoteMode();
        //die(var_dump($content));
        $voteMode = $this->em->getRepository(VoteMode::class)->find($voteModeId);
        if(! $voteMode){
            return new Response("Vote type not found");
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
        $payment =  new Payment();

        $vote = new Vote();
        $vote->setArtist($artist);
        $vote->setNumberOfVote($voteMode->getNumberOfVote());
        $vote->setPrime($prime);
        $vote->setIsPayed(false);
        $this->em->persist($vote);

        $payment->setAmount($voteMode->getPrice());
        $payment->setCurrency($currency);
        $payment->setVote($vote);
        $payment->setStatus('PENDING');
        $payment->setEmail("");
        $payment->setPhone("");
        $payment->setUsername("");
        $r = substr($competition->getCode(),0,2).''.$prime->getId();
        $payment->setReference(uniqid($r));
        $this->em->persist($payment);

        $this->em->flush();

        $session = $request->getSession();
        $session->set('reference',$payment->getReference());
        
        return $this->redirect($this->payUrl->paymentUrl($payment));

        }

        $artists = $this->em->getRepository(Artist::class)->findAll();
        $vmodes = $this->em->getRepository(VoteMode::class)->findAll();
        $artistArray = array_chunk($artists,4);
        

        return $this->render('vote_process/votelist.html.twig', [
            'controller_name' => 'VoteProcessController',
            'artistArray'=>$artistArray,
            'voteModes'=>$vmodes,
            'artists'=>$artists
        ]);
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
            $payment->setStatus('PAYED');
            $vote->setPayment($payment);
            $this->em->flush();
            $session->remove('reference');

           
          
        
        return $this->render('vote_process/success.html.twig', [
            'controller_name' => 'VoteProcessController',
            'candidate'=>$vote->getArtist(),
            'vote'=>$vote
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
            $vote->setPayment($payment);
            $this->em->flush();
           
        
            return $this->render('vote_process/failed.html.twig', [
                'status' => 'failed',
                'candidate'=>$vote->getArtist(),
                'vote'=>$vote
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
            $vote->setPayment($payment);
            $this->em->flush();
           
        return $this->render('vote_process/failed.html.twig', [
            'status' => 'canceled',
            'candidate'=>$vote->getArtist(),
            'vote'=>$vote
        ]);

    }
}
