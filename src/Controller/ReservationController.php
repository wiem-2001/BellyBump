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

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }

    public function EventReservation( $userId , $eventId,EventRepository $eventRepository,UserRepository $userRepository): Response
    {
        $user=$userRepository->find($userId);
        $event = $eventRepository->find($eventId);
        if ($user->getRoles()[0]=="ROLE_MOTHER") {
            $user->addEvent($event);
            $event->addReservation( $user );
            
        }
        return $this->render('reservation/MotherEventList');
        
    }

    public function CancelReservation( $userId , $eventId,EventRepository $eventRepository,UserRepository $userRepository): Response
    {
        $user=$userRepository->find($userId);
        $event = $eventRepository->find($eventId);
        if ($user->getRoles()[0]=="ROLE_MOTHER") {
            $user->removeEvent($event);
            $event->removeReservation( $user );
            
        }
        return $this->render('reservation/MotherEventList');
        
    }
}
