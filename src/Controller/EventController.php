<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Form\UpdateEventType;
use App\Repository\CoachRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;

class EventController extends AbstractController
{
    #[Route('/manageEvents', name: 'list_event')]
    public function EventsList(EventRepository $repository,CoachRepository $coachRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $realizedEvents=$repository->findRealizedEvents();
        $notrealizedEvents=$repository->findNotRealizedEvents();
        $Events = $repository->findAll();
        $Coachs= $coachRepository->findAll();
        return $this->render("Event/AdminEventList.html.twig",array('notrealizedEvents'=>$notrealizedEvents,'realizedEvents'=>$realizedEvents,'tabEvents'=>$Events,'tabCoach'=>$Coachs));
    }

    #[Route('/eventsList', name: 'list_event_mother')]
public function EventsListMother(Request $request, Security $security, EventRepository $repository, UserRepository $userRepository)
{
    $this->denyAccessUnlessGranted('ROLE_MOTHER');

    //$mother = $userRepository->find(1);
    $mother=$security->getUser();
    $Events = $repository->MotherNotParticipatedEvents($mother);
    $triOption = $request->query->get('tri');

    if ($triOption == 'date') {
        $sortedEvents = $repository->sortEventsByDate($mother);
        dump($sortedEvents); // Debug statement
    } elseif ($triOption == 'coach') {
        $sortedEvents = $repository->sortEventsByCoach($mother);
        dump($sortedEvents); // Debug statement
    } else {
        // Default behavior when no sorting option is provided
        $sortedEvents = $Events;
        dump($sortedEvents); // Debug statement
    }

    return $this->render("reservation/MotherEventList.html.twig", [
        'tabEvents' => $sortedEvents,
        'user'=>$mother
    ]);
}


    // ADD NEW EVENT
    #[Route('/addEvent', name: 'event_add')]
    public function addEvent(Request $request, ManagerRegistry $managerRegistry): Response
    {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validate the uploaded file
            $file = $form->get('image')->getData();
            if ($file instanceof UploadedFile) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileExtension = $file->guessExtension();

                if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                    $this->addFlash('error', 'Only JPG, JPEG, and PNG files are allowed.');
                    return $this->redirectToRoute('event_add');
                }

                // Handle file upload and entity persisting
                $fileName = md5(uniqid()).'.'.$fileExtension;
                $file->move($this->getParameter('images_directory_event'), $fileName);
                $event->setImage($fileName);
                
                $entityManager = $managerRegistry->getManager();
                $entityManager->persist($event);
                $entityManager->flush();

                return $this->redirectToRoute('list_event');
            }
        }

        return $this->render('event/AddEvent.html.twig', [
            'form' => $form->createView(),
        ]);
    }



   //UPDATE  AN EXISTING EVENT ***********************************************************************************
   #[Route('/updateEvent/{id}', name: 'event_update')]
public function updateEvent(Request $request, $id, ManagerRegistry $managerRegistry, EventRepository $repository): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $event = $repository->find($id);
    $oldFileName = $event->getImage();

    $form = $this->createForm(UpdateEventType::class, $event);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $event = $form->getData();

        // Handle null values for optional fields
        $eventName = $form->get('name')->getData();
        if ($eventName !== null) {
            $event->setName($eventName);
        }

        $eventDescription = $form->get('description')->getData();
        if ($eventDescription !== null) {
            $event->setDescription($eventDescription);
        }

        $meetingCode = $form->get('MeetingCode')->getData();
        if ($meetingCode !== null) {
            $event->setMeetingCode($meetingCode);
        }

        // Validate the uploaded file
        $file = $form->get('image')->getData();
        if ($file instanceof UploadedFile) {
            // Handle file upload and entity updating
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();

            // Move the file to the directory where images are stored
            $file->move(
                $this->getParameter('images_directory_event'),
                $newFilename
            );

            // Delete the old file if it exists
            $oldFilePath = $this->getParameter('images_directory_event') . '/' . $oldFileName;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            // Update the image property of the event
            $event->setImage($newFilename);
        }

        // Update the event entity in the database
        $entityManager = $managerRegistry->getManager();
        $entityManager->flush();

        $this->addFlash('success', 'Event updated successfully.');

        return $this->redirectToRoute('list_event');
    }

    return $this->render('event/updateEvent.html.twig', [
        'form' => $form->createView(),
    ]);
}




    #[Route('/deleteEvent/{id}', name:"event_delete")]
    public function deleteEvent($id,EventRepository $repository,ManagerRegistry $managerRegistry , Request $request)
    {
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $event= $repository->find($id);

        if(!$event){throw $this->createNotFoundException('Event not found');}
            $oldFileName = $event->getImage();
            // Delete the old file if it exists
                        
            $oldFilePath = $this->getParameter('images_directory_event') . '/' . $oldFileName;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
            $em=$managerRegistry->getManager();
            $em->remove($event);
            $em->flush();
            return $this->redirectToRoute("list_event");    
    }



    

}
