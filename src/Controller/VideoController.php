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
}
