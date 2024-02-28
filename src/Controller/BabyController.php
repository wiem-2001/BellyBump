<?php
//src/controller/BabyController
namespace App\Controller;

use App\Entity\Baby;
use App\Form\BabyType;
use App\Repository\BabyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormError;
use App\Service\PdfGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;



#[Route('/baby')]
class BabyController extends AbstractController
{
    #[Route('/download-pdf', name: 'app_baby_download_pdf', methods: ['GET'])]
    public function downloadPdf(PdfGenerator $pdfGenerator): Response
    {
        return $pdfGenerator->generateBabyListPdf();
    }

    #[Route('/', name: 'app_baby_index', methods: ['GET'])]
    public function index(BabyRepository $babyRepository): Response
    {
        return $this->render('baby/index.html.twig', [
            'babies' => $babyRepository->findAll(),
        ]);
    }
    #[Route('/search', name: 'app_baby_search', methods: ['GET'])]
public function search(Request $request, BabyRepository $babyRepository): JsonResponse
{
    $query = $request->query->get('q');
    $results = $babyRepository->findBySearchQuery($query); // Implement findBySearchQuery method in your repository

    $formattedResults = [];
    foreach ($results as $result) {
        // Format results as needed
        $formattedResults[] = [
            'nom' => $result->getNom(),
            'prenom' => $result->getPrenom(),
            'sexe' => $result->getSexe(),
            'taille' => $result->getTaille(),
            'poids' => $result->getPoids(),
            'dateNaissance' => $result->getDateNaissance(),
            // Add other fields as needed
        ];
    }

    return new JsonResponse($formattedResults);
}
    

    #[Route('/babies', name: 'app_babies_index', methods: ['GET'])]
    public function indexx(BabyRepository $babyRepository): Response
    {
        return $this->render('baby\frontBaby.html.twig', [
            'babies' => $babyRepository->findAll(),
        ]);
    }


// ...

#[Route('/new', name: 'app_baby_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $baby = new Baby();
    $form = $this->createForm(BabyType::class, $baby);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $currentDate = new \DateTime();
        if ($baby->getDateNaissance() > $currentDate) {
            $form->get('dateNaissance')->addError(new FormError('Date of birth cannot be in the future.'));
            return $this->renderForm('baby/new.html.twig', [
                'baby' => $baby,
                'form' => $form,
            ]);
        }

        $entityManager->persist($baby);
        $entityManager->flush();

        // Redirect to the InfoMedicaux new page with baby id as a parameter
        return $this->redirectToRoute('app_info_medicaux_new', ['babyId' => $baby->getId()], Response::HTTP_SEE_OTHER);
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
