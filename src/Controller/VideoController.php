<?php

namespace App\Controller;

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

                $views = array_diff($views,[$content->uid]);
               
                # code...
            }else{
                array_push($views,$content->uid);
                
            }
            $video->setViews($views);
               $this->em->flush();
        }
        return $this->json(["success"=>true,"video"=>$video,"uid"=>$content->uid]);
    }
}
