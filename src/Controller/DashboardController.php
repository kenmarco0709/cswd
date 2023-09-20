<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\BranchVariableEntity;
use App\Entity\ClientAccountPaymentEntity;
use App\Entity\ClientAccountEntity;
use App\Entity\ClientAccountBillingEntity;
use App\Entity\PurokEntity;


use App\Service\AuthService;

/**
 * @Route("/dashboard")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("", name="dashboard_index")
     */
    public function index(Request $request, AuthService $authService)
    {

        if(!$authService->isLoggedIn()) return $authService->redirectToLogin();

       return $this->render('Dashboard/index.html.twig', ['javascripts' => array('/js/dashboard/index.js')] );

    }    
}
