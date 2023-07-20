<?php

namespace App\Controller;

use App\Entity\UserData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ReportingController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }
    #[Route('/reporting', name: 'app_reporting')]
    public function index(): Response
    {
        return $this->render('reporting/index.html.twig', [
            'controller_name' => 'ReportingController',
        ]);
    }

    #[Route("/api/block-user",name:'app_block_user',methods:'POST')]
    public function blockUser(Request $request):Response{
        $content = json_decode($request->getContent());
        $userData = $this->em->getRepository(UserData::class)->find($content->id);
        if ($userData != null) {
            # code...
            $blockedUsers = $userData->getBlokedUsers();
            if (in_array($content->uid,$blockedUsers)) {

            $blockedUsers = array_diff($blockedUsers,[$content->uid]);
               
                # code...
            }else{
                array_push($blockedUsers,$content->uid);
                
            }
           
            $userData->setBlokedUsers($blockedUsers);
               $this->em->flush();
        }
        return $this->json(["success"=>true,"userData"=>$userData,"uid"=>$content->uid]);
    }
}
