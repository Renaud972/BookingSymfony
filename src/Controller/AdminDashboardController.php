<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Services\Statistics;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, Statistics $statsService)
    {
        $stats = $statsService->getStatistics();

        $bestAds= $statsService->getAdsStats('DESC');

        $worstAds= $statsService->getAdsStats('ASC');
                  
        return $this->render('admin/dashboard/index.html.twig', [
            'stats'=>$stats,
            'bestAds'=>$bestAds,
            'worstAds'=>$worstAds
        ]);
    }
}
