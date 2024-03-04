<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {

        return $this->render('test/index.html.twig', [

            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/testTemplate', name: 'app_testTemplate')]

    public function indexTest(Security $security): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MOTHER');
        $user = $security->getUser();
        return $this->render('test/index.html.twig',[
          'user'=>$user
        ]);
    }
}
