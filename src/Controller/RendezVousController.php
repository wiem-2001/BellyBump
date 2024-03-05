<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rendez/vous')]
class RendezVousController extends AbstractController
{
    #[Route('/', name: 'app_rendez_vous_index', methods: ['GET'])]
    public function index(RendezVousRepository $rendezVousRepository): Response
    {
        return $this->render('rendez_vous/index.html.twig', [
            'rendez_vouses' => $rendezVousRepository->findAll(),
        ]);
    }
    #[Route('/front/{id}', name: 'app_rendez_vous_front', methods: ['GET'])]
    public function redezfront(RendezVousRepository $rendezVousRepository, $id): Response
    {
        $doctorAppointments = $rendezVousRepository->findBy(['nomMed' => $id]);
    
        return $this->render('rendez_vous/showRendez.html.twig', [
            'rendez_vouses' => $doctorAppointments,
        ]);
    }
    
    #[Route('/new', name: 'app_rendez_vous_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $rendezVou = new RendezVous();
    $form = $this->createForm(RendezVousType::class, $rendezVou);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Check if similar rendezvous exists
        $existingRendezVous = $entityManager->getRepository(RendezVous::class)->findOneBy([
            'nomMed' => $rendezVou->getNomMed(),
            'DateReservation' => $rendezVou->getDateReservation(),
            'heureReservation' => $rendezVou->getHeureReservation(),
        ]);

        if ($existingRendezVous) {
            $this->addFlash('error', 'Un rendez-vous existe déjà à cette date et heure.');
            return $this->redirectToRoute('app_rendez_vous_new');
        }

        $entityManager->persist($rendezVou);
        $entityManager->flush();

        return $this->redirectToRoute('app_etabFront_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('rendez_vous/new.html.twig', [
        'rendez_vou' => $rendezVou,
        'form' => $form,
    ]);
}
   

    #[Route('/{id}', name: 'app_rendez_vous_show', methods: ['GET'])]
    public function show(RendezVous $rendezVou): Response
    {
        return $this->render('rendez_vous/show.html.twig', [
            'rendez_vou' => $rendezVou,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rendez_vous_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RendezVous $rendezVou, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezVousType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendez_vous/edit.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendez_vous_delete', methods: ['POST'])]
    public function delete(Request $request, RendezVous $rendezVou, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezVou->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rendezVou);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }
    
 
}