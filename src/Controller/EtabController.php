<?php

namespace App\Controller;

use App\Entity\Etab;
use App\Form\EtabType;
use App\Repository\EtabRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/etab')]
class EtabController extends AbstractController
{
    #[Route('/', name: 'app_etab_index', methods: ['GET'])]
    public function index(EtabRepository $etabRepository): Response
    {
        return $this->render('etab/index.html.twig', [
            'etabs' => $etabRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_etab_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etab = new Etab();
        $form = $this->createForm(EtabType::class, $etab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etab);
            $entityManager->flush();

            return $this->redirectToRoute('app_etab_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('etab/new.html.twig', [
            'etab' => $etab,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etab_show', methods: ['GET'])]
    public function show(Etab $etab): Response
    {
        return $this->render('etab/show.html.twig', [
            'etab' => $etab,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etab_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etab $etab, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtabType::class, $etab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_etab_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('etab/edit.html.twig', [
            'etab' => $etab,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etab_delete', methods: ['POST'])]
    public function delete(Request $request, Etab $etab, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etab->getId(), $request->request->get('_token'))) {
            $entityManager->remove($etab);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etab_index', [], Response::HTTP_SEE_OTHER);
    }
}
