<?php

namespace App\Controller;

use App\Entity\Baby;
use App\Form\BabyType;
use App\Repository\BabyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/baby')]
class BabyController extends AbstractController
{
    #[Route('/', name: 'app_baby_index', methods: ['GET'])]
    public function index(BabyRepository $babyRepository): Response
    {
        return $this->render('baby/index.html.twig', [
            'babies' => $babyRepository->findAll(),
        ]);
    }
    #[Route('/babies', name: 'app_babies_index', methods: ['GET'])]
    public function indexx(BabyRepository $babyRepository): Response
    {
        return $this->render('baby\frontBaby.html.twig', [
            'babies' => $babyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_baby_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $baby = new Baby();
        $form = $this->createForm(BabyType::class, $baby);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($baby);
            $entityManager->flush();

            return $this->redirectToRoute('app_baby_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('baby/new.html.twig', [
            'baby' => $baby,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_baby_show', methods: ['GET'])]
    public function show(Baby $baby): Response
    {
        return $this->render('baby/show.html.twig', [
            'baby' => $baby,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_baby_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Baby $baby, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BabyType::class, $baby);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_baby_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('baby/edit.html.twig', [
            'baby' => $baby,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_baby_delete', methods: ['POST'])]
    public function delete(Request $request, Baby $baby, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$baby->getId(), $request->request->get('_token'))) {
            $entityManager->remove($baby);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_baby_index', [], Response::HTTP_SEE_OTHER);
    }
}
