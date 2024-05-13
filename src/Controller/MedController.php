<?php

namespace App\Controller;

use App\Entity\Med;
use App\Entity\Etab;
use App\Form\MedType;
use App\Repository\MedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/med')]
class MedController extends AbstractController
{
    #[Route('/', name: 'app_med_index', methods: ['GET'])]
    public function index(MedRepository $medRepository): Response
    {
        $meds = $medRepository->findAll();

        return $this->render('med/index.html.twig', [
            'meds' => $meds,
        ]);
    }

    #[Route('/medFront/{id}', name: 'app_medFront_index', methods: ['GET'])]
    public function showMedFront(MedRepository $medRepository, Etab $etab, Security $security): Response
    {
        $user = $security->getUser();
        $meds = $medRepository->findBy(['etab' => $etab]);

        return $this->render('med/medFront.html.twig', [
            'meds' => $meds,
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'app_med_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $med = new Med();
        $form = $this->createForm(MedType::class, $med);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($med);
            $entityManager->flush();

            return $this->redirectToRoute('app_med_index');
        }

        return $this->render('med/new.html.twig', [
            'med' => $med,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_med_show', methods: ['GET'])]
    public function show(int $id, MedRepository $medRepository): Response
    {
        // Récupérer l'entité Med à afficher en fonction de son identifiant
        $med = $medRepository->find($id);

        // Vérifier si l'entité Med existe
        if (!$med) {
            throw $this->createNotFoundException('Médecin introuvable avec l\'identifiant: '.$id);
        }

        // Rendre la vue de détail avec l'entité Med
        return $this->render('med/show.html.twig', [
            'med' => $med,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_med_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, MedRepository $medRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'entité Med à éditer en fonction de son identifiant
        $med = $medRepository->find($id);

        // Vérifier si l'entité Med existe
        if (!$med) {
            throw $this->createNotFoundException('Médecin introuvable avec l\'identifiant: '.$id);
        }

        // Créer le formulaire pour éditer l'entité Med
        $form = $this->createForm(MedType::class, $med);
        $form->handleRequest($request);

        // Traiter le formulaire soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_med_index');
        }

        // Rendre la vue d'édition avec le formulaire et l'entité Med
        return $this->render('med/edit.html.twig', [
            'med' => $med,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'app_med_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, MedRepository $medRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'entité Med à supprimer en fonction de son identifiant
        $med = $medRepository->find($id);

        // Vérifier si l'entité Med existe
        if (!$med) {
            throw $this->createNotFoundException('Médecin introuvable avec l\'identifiant: '.$id);
        }

        // Vérifier si le jeton CSRF est valide
        if ($this->isCsrfTokenValid('delete'.$med->getId(), $request->request->get('_token'))) {
            // Supprimer l'entité Med de la base de données
            $entityManager->remove($med);
            $entityManager->flush();
        }

        // Rediriger vers la liste des médecins après la suppression
        return $this->redirectToRoute('app_med_index');
    }
}
