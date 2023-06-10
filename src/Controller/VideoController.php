<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Entity\Prime;
use App\Entity\UserData;
use App\Entity\VideoData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }
    #[Route('/api/video', name: 'app_video')]
    public function index(): Response
    {
        return $this->render('video/index.html.twig', [
            'controller_name' => 'VideoController',
        ]);
    }
    #[Route("/api/video/like", name:'app_video_liked',methods:'GET')]
    public function getUserLikedVideo(Request $request){

        $videos = $this->em->getRepository(VideoData::class)->findAll();
        $videoList = array();
        if($videos)
        foreach ($videos as $key => $value) {
            # code...
            if (in_array($request->query->get('uid'),$value->getLikes())) {

                array_push($videoList,$value);
                # code...
            }

        }

        return $this->json($videoList);


    }
    #[Route("/api/video/user-like",name:'app_video_user_like',methods:'POST')]
    public function setUserLikeVideo(Request $request):Response{
        $content = json_decode($request->getContent());
        $video = $this->em->getRepository(VideoData::class)->find($content->id);
        if ($video != null) {
            # code...
            $likes = $video->getLikes();
            if (in_array($content->uid,$likes)) {

               $likes = array_diff($likes,[$content->uid]);
               
                # code...
            }else{
                array_push($likes,$content->uid);
                
            }
            $video->setLikes($likes);
               $this->em->flush();
        }
        return $this->json(["success"=>true,"video"=>$video,"uid"=>$content->uid]);
    }
    #[Route("/api/video/user-view",name:'app_video_user_view',methods:'POST')]
    public function setUserViewVideo(Request $request):Response{
        $content = json_decode($request->getContent());
        $video = $this->em->getRepository(VideoData::class)->find($content->id);
        if ($video != null) {
            # code...
            $views = $video->getViews();
            if (in_array($content->uid,$views)) {

                //$views = array_diff($views,[$content->uid]);
               
                # code...
            }else{
                array_push($views,$content->uid);
                
            }
            $video->setViews($views);
               $this->em->flush();
        }
        return $this->json(["success"=>true,"video"=>$video,"uid"=>$content->uid]);
    }

    #[Route("/api/video/check", name:'app_video_check', methods:'POST')]
    public function checkVideoToUpload(Request $request):Response
    {
        /*
        {
            primeId:1,
            userId:2
        }

        */
        $content = json_decode($request->getContent());
        $prime = $this->em->getRepository(Prime::class)->find($content->primeId);
        $cd =  $this->em->getRepository(UserData::class)->find($content->userId);
        if ($prime && $cd) {
            # code...
            if ($prime->isIsActive() ) {
                # code...
                if ($cd->getStatus() == "IN") {
                    # code...
                    if ($cd->getChurchFile() != null && $cd->getChurchFile() != "not file" && $cd->getChurchFile() != "") {
                        # code...
                        return $this->json(["success"=>true,"message_en"=>"Valid request","message_fr"=>"Demande valide"],200);
                    }else {
                        # code...
                        return $this->json(["success"=>false,"message_en"=>"Please upload church letter","message_fr"=>"Veuillez envoyer la lettre du pasteur"],403);
                    }
                    
                }else {
                    return $this->json(["success"=>false,"message_en"=>"User is OUT", "message_fr"=>"Candidate est elimine"],401);
                }
                
            }else {
                return $this->json(["success"=>true,"message_en"=>"Prime is not active","message_fr"=>"Prime n'est pas active"],400);
            }

        }else{
            return $this->json(["success"=>false,"message_en"=>"Prime not exist","message_fr"=>"Prime n'existe pas"],404);
        }
    }
    #[Route("/api/current-prime/{code}", name:'app_current_prime', methods:'GET')]
    public function getCurrentPrime( $code){
        $competition = $this->em->getRepository(Competition::class)->findOneBy(['code'=>$code]);
        if ($competition) {
            # code...
            $prime = $this->em->getRepository(Prime::class)->findOneBy(['competitionId'=>$competition->getId(),'isActive'=>true]);
            return $this->json($prime,200);

        }else {
            # code...
            return $this->json(["success"=>false,"message"=>"Prime not found"]);
        }
        

    }
}
