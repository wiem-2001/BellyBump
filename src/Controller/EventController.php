<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Form\UpdateEventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class EventController extends AbstractController
{
    #[Route('/eventsListAdmin', name: 'list_event')]
    public function EventsList(EventRepository $repository)
    {
        $Events = $repository->findAll();
        
        return $this->render("Event/AdminEventList.html.twig",array('tabEvents'=>$Events));
    }

    #[Route('/eventsList', name: 'list_event_mother')]
    public function EventsListMother(EventRepository $repository)
    {
        $Events = $repository->findAll();
        return $this->render("reservation/MotherEventList.html.twig",array('tabEvents'=>$Events));
    }

    // ADD NEW EVENT
    #[Route('/addEvent', name: 'event_add')]
    public function addEvent(Request $request, ManagerRegistry $managerRegistry): Response
    {
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
                $file->move($this->getParameter('images_directory'), $fileName);
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



    //UPDATE  AN EXISTING EVENT
    #[Route('/updateEvent/{id}', name: 'event_update')]
    public function updateEvent(Request $request,  $id, ManagerRegistry $managerRegistry,EventRepository $repository): Response
    {
        $event = $repository->find($id);
        $oldFileName = $event->getImage();

        $form = $this->createForm(EventType::class, $event);
        //$form->get('image')->setData(new File($this->getParameter('images_directory').$oldFileName));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validate the uploaded file
                $file = $form->get('image')->getData();
                if($file instanceof UploadedFile) {
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];
                    $fileExtension = $file->guessExtension();
    
                    if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                        $this->addFlash('error', 'Only JPG, JPEG, and PNG files are allowed.');
                        return $this->redirectToRoute('event_update', ['id' => $id]);
                    }
    
                    // Delete the old file if it exists
                    
                    $oldFilePath = $this->getParameter('images_directory') . '/' . $oldFileName;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                    // Handle file upload and entity updating
                    $fileName = md5(uniqid()) . '.' . $fileExtension;
                    $file->move($this->getParameter('images_directory'), $fileName);
                    $event->setImage($fileName);
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
    public function deleteBook($id,EventRepository $repository,ManagerRegistry $managerRegistry , Request $request)
    {
        $event= $repository->find($id);

        if(!$event){throw $this->createNotFoundException('Event not found');}
        
            $em=$managerRegistry->getManager();
            $em->remove($event);
            $em->flush();
            return $this->redirectToRoute("list_event");    
    }



    #[Route('/calender', name: 'mother_calender')]
    public function CalenderDisplay(EventRepository $repository)
    {
        $Events = $repository->findAll();
        return $this->render("calender/calenderDisplay.html.twig",array('events'=>$Events));
    }

}
