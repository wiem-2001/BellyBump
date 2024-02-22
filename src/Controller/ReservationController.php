<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;

class ReservationController extends AbstractController
{

    #[Route('/calender', name: 'mother_calender')]
    public function CalenderDisplay(Security $security,EventRepository $repository,UserRepository $userRepository)
    {
        $mother=$userRepository->find(2);
        //$mother=$security->getUser();
        $Events = $repository->MotherParticipatedEvents($mother);//creer une fonction dans le repository  pour récupérer les événements d'une maman authentifier 
        return $this->render("calender/calenderDisplay.html.twig",array('events'=>$Events));
    }


    #[Route('/reserveEvent/{id}', name: 'reserve_event')]
    public function EventReservation(Security $security , $id,EventRepository $eventRepository,UserRepository $userRepository,ManagerRegistry $managerRegistry): Response
    {
        //$user = $security->getUser();
        $user=$userRepository->find(2);
        $event = $eventRepository->find($id);
        if ($user) {
            // Check the role of the user
            if (in_array('ROLE_MOTHER', $user->getRoles())) {
                // If the user is a mother, add the participation 
                $user->addEvent($event);
                $event->addReservation( $user );
                $entityManager = $managerRegistry->getManager();
                $entityManager->flush();
                return $this->redirectToRoute('mother_calender');               }
        }
        /*$userId=2;
        $user=$userRepository->find($userId);
        $event = $eventRepository->find($id);
        if ($user->getRoles()[0]=="ROLE_MOTHER") {
            $user->addEvent($event);
            $event->addReservation( $user );    
        }*/    
        return $this->redirectToRoute('mother_calender');           
    }

    #[Route('/cancelEventReservation/{eventId}', name: 'cancel_reservation')]
    public function CancelReservation( Security $security , $eventId,EventRepository $eventRepository,ManagerRegistry $managerRegistry,UserRepository $userRepository): Response
    {
        //$user = $security->getUser();
        $user=$userRepository->find(2);
        $event = $eventRepository->find($eventId);
        if ($user) {
            // Check the role of the user
            if (in_array('ROLE_MOTHER', $user->getRoles())) {
                // If the user is a mother, add the participation 
                $user->removeEvent($event);
                $event->removeReservation( $user );
                $entityManager = $managerRegistry->getManager();
                $entityManager->flush();
                return $this->redirectToRoute('mother_calender');
            }
        }
        
        return $this->redirectToRoute('mother_calender');        
    }
}
