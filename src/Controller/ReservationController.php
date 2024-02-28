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
use App\Security\EventInvitation;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class ReservationController extends AbstractController
{
    private EventInvitation $meetInvite;

    public function __construct(EventInvitation $meetInvite) {
        $this->meetInvite =  $meetInvite;
    }

    #[Route('/calender', name: 'mother_calender')]
    public function CalenderDisplay(Security $security,EventRepository $repository,UserRepository $userRepository)
    {
    $this->denyAccessUnlessGranted('ROLE_MOTHER');

      //  $mother=$userRepository->find(1);
        $mother=$security->getUser();
        $Events = $repository->MotherParticipatedEvents($mother);//creer une fonction dans le repository  pour récupérer les événements d'une maman authentifier 
        return $this->render("calender/calenderDisplay.html.twig",array('events'=>$Events,'user'=>$mother));
    }


    #[Route('/reserveEvent/{id}', name: 'reserve_event')]
    public function EventReservation(Security $security , $id,EventRepository $eventRepository,UserRepository $userRepository,ManagerRegistry $managerRegistry): Response
    {
    $this->denyAccessUnlessGranted('ROLE_MOTHER');

        $user = $security->getUser();
        //$user=$userRepository->find(1);
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
    $this->denyAccessUnlessGranted('ROLE_MOTHER');

        $user = $security->getUser();
        //$user=$userRepository->find(1);
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
    #[Route("/send-meeting-invite/{id}", name:"send_meeting_invite")]
    public function LunchEvent($id,EventRepository $eventRepository,ManagerRegistry $managerRegistry){
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $event= $eventRepository->find($id);
         $users= $event->getReservation();
         if ($users==null){
            // cant  lunch event because no one has reserved it yet.
            //show alert
         }
         else{
          foreach ($users as $user ) {
            $this->meetInvite->sendMeetingInvite($event,
            (new TemplatedEmail())
                ->from(new Address('bellybump4@gmail.com', 'BellyBump'))
                ->to($user->getEmail())
                ->subject('Meeting Invitation: '.$event->getName())
                ->htmlTemplate('reservation/meeting_invite.html.twig') // Your Twig template for meeting invitation
            ); 
         }
         $event->setLaunched(True);
         $entityManager= $managerRegistry->getManager();
         $entityManager->flush();
         return $this->redirectToRoute('list_event');

        }
}
}
