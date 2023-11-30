<?php

namespace App\Controller;

use App\Entity\Prime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoteStatController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }
    #[Route('admin/vote/stat', name: 'app_admin_vote_stat')]
    public function index(Request $request): Response
    { 
        $primes = $this->em->getRepository(Prime::class)->findAll();
        $sql = '
        SELECT first_name,last_name,numero, (SELECT SUM(`vote`.`number_of_vote`) FROM `vote` WHERE 
        `vote`.`artist_id` = `artist`.`id` AND `vote`.`prime_id` =:primeId AND `vote`.`is_payed` = 1) 
        AS total FROM `artist` WHERE `artist`.is_active = 1;';
        $criteria = [];
        $prime_name = "All";
        if($request->query->has('prime') && $request->query->get('prime') !== NULL && $request->query->get('prime') !== ""){
           if($request->query->get('prime') == 0){
            $sql = '
            SELECT first_name,last_name,numero, (SELECT SUM(`vote`.`number_of_vote`) FROM `vote` WHERE 
            `vote`.`artist_id` = `artist`.`id` AND  `vote`.`is_payed` = 1) 
            AS total FROM `artist` WHERE `artist`.is_active = 1;';
           }else{
            
            $prime = $this->em->getRepository(Prime::class)->find($request->query->get('prime'));
            $criteria = ['primeId' => $prime->getId()];
            $prime_name = $prime->getName();
           }
            

        }else{
            $prime = $this->em->getRepository(Prime::class)->findOneBy(['isActive'=>true]);
            $criteria = ['primeId' => $prime->getId()];
            $prime_name = $prime->getName();
        }


        $arraysChart = array(['Artist', 'total vote']);;
        
        $conn = $this->em->getConnection();
        $resultSet = $conn->executeQuery($sql, $criteria);
        $res = $resultSet->fetchAllAssociative();
        if($res){
            foreach($res as $r){
                $name = $r['first_name'].' '.$r['last_name'];
                
                if($r["total"]){
                    $total = $r["total"];
                    
                }else{
                    $total = 0;
                }
                array_push($arraysChart,[$name,(int)$total]);

            }

        }
        //dd($arraysChart);

        $chart = new \CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart();
        $chart->getData()->setArrayToDataTable($arraysChart);
        $chart->getOptions()->setTitle('Vote Chart');
        $chart->getOptions()->getHAxis()->setTitle('Total');
        $chart->getOptions()->getHAxis()->setMinValue(0);
        $chart->getOptions()->getVAxis()->setTitle('Artist');
        $chart->getOptions()->setWidth(600);
        $chart->getOptions()->setHeight(600);
        

        return $this->render('vote_stat/index.html.twig', [
            'controller_name' => 'VoteStatController',
            'results'=>$res,
            'prime'=>$prime_name,
            'barchart'=>$chart,
            'primes'=>$primes
        ]);
    }
}
