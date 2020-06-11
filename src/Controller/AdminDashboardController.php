<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager, StatsService $statsService)
    {
        // $users = $statsService->getUsersCount();
        // $ads = $statsService->getAdsCount();
        // $bookings = $statsService->GetBookingsCount();
        // $comments = $statsService->getCommentsCount();

        $stats    = $statsService->getStats();
        $bestAds  = $statsService->getAdsStats('DESC');
        $worstAds = $statsService->getAdsStats('ASC');

        dump($bestAds);
        
        return $this->render('admin/dashboard/index.html.twig', [
            'stats'    => $stats,
            'bestAds'  => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
