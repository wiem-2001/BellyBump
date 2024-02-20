<?php

namespace App\Controller;

use App\Entity\Med;
use App\Form\MedType;
use App\Repository\MedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/med')]
class MedController extends AbstractController
{
    #[Route('/', name: 'app_med_index', methods: ['GET'])]
    public function index(MedRepository $medRepository): Response
    {
        return $this->render('med/index.html.twig', [
            'meds' => $medRepository->findAll(),
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

            return $this->redirectToRoute('app_med_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('med/new.html.twig', [
            'med' => $med,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_med_show', methods: ['GET'])]
    public function show(Med $med): Response
    {
        return $this->render('med/show.html.twig', [
            'med' => $med,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_med_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Med $med, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MedType::class, $med);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_med_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('med/edit.html.twig', [
            'med' => $med,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_med_delete', methods: ['POST'])]
    public function delete(Request $request, Med $med, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$med->getId(), $request->request->get('_token'))) {
            $entityManager->remove($med);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_med_index', [], Response::HTTP_SEE_OTHER);
    }
}
