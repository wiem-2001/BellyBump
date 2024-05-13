<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Form\CoachType;
use App\Form\UpdateCoachType;
use App\Repository\CoachRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CoachController extends AbstractController
{
    //SHOW  ALL COACHES TABLE   
    // this will be in Event controller to visulize it in /manageEvents page 

    //ADD COACH
    #[Route('/addCoach', name :"coach_add")]
    public function addCoach(Request $request,ManagerRegistry $managerRegistry)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $coach = new Coach();
        //create form for adding coach
        $form=$this->createForm(CoachType::class,$coach);
        //allow to get data from the request (POST method)
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //get the entity manager from the service container
            $entityManager= $managerRegistry->getManager();
            
            //
            $entityManager -> persist($coach);
            $entityManager -> flush();

           return $this->redirectToRoute("list_event");
        }
       return $this->render('coach/addCoach.html.twig', ['form'=>$form->createView()]);
    
    }


    //UPDATE  COACH
    #[Route('/updateCoachDetails/{id}', name: 'coach_update')]
    public function updateCoachDetails(int $id,CoachRepository $coachRepository, Request $request , ManagerRegistry $managerRegistry)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //find the coach by id
        $coach = $coachRepository->find($id);
        //create formular
        $form= $this->createForm(UpdateCoachType::class , $coach);
        //send request
        $form->handleRequest($request);
        //verify
        if($form->isSubmitted() && $form->isValid()){
            $entityManager=	 $managerRegistry->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('list_event');
        }
        return $this->renderForm("coach/updateCoach.html.twig",["form"=>$form]);  
    }


    // DELETE  COACH

    #[Route('/deleteCoach/{id}', name:"coach_delete")]
    public function deleteCoach (Request $request,$id ,CoachRepository $repository,ManagerRegistry $managerRegistry) 
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $coach=$repository->find($id);
        if (!$coach) {
            throw new NotFoundHttpException("this Coach is not existe.");
          } 
        $events = $coach->getCochedEvents();
        foreach ($events as $event) {
            $event->setCoach(null);
        }
        $entityManager= $managerRegistry->getManager();
        $entityManager->remove($coach);
        $entityManager->flush();
        return $this->redirectToRoute("list_event"); 

    }
}