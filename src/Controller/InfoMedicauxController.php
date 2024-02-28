<?php

namespace App\Controller;

use App\Entity\InfoMedicaux;
use App\Entity\Baby;
use App\Form\InfoMedicauxType;
use App\Repository\InfoMedicauxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormError;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/info/medicaux')]
class InfoMedicauxController extends AbstractController
{

    #[Route('/', name: 'app_info_medicaux_index', methods: ['GET'])]
public function index(Request $request, PaginatorInterface $paginator, InfoMedicauxRepository $infoMedicauxRepository): Response
{
    $queryBuilder = $infoMedicauxRepository->createQueryBuilder('i');

    // Paginate the query
    $pagination = $paginator->paginate(
        $queryBuilder, // Query to paginate
        $request->query->getInt('page', 1), // Current page number
        4 // Number of items per page
    );

    return $this->render('info_medicaux/index.html.twig', [
        'pagination' => $pagination,
    ]);
}
    

    #[Route('/frontInfo', name: 'app_frontInfo_index', methods: ['GET'])]
    public function indexxx(InfoMedicauxRepository $infoMedicauxRepository): Response
    {
        return $this->render('info_medicaux\frontInfo.html.twig', [
            'info_medicauxes' => $infoMedicauxRepository->findAll(),
        ]);
    }

    #[Route('/search', name: 'app_info_medicaux_search', methods: ['GET'])]
    public function search(Request $request, InfoMedicauxRepository $infoMedicauxRepository): JsonResponse
    {
        $query = $request->query->get('q');
        $results = $infoMedicauxRepository->findBySearchQuery($query); // Implement findBySearchQuery method in your repository
    
        $formattedResults = [];
        foreach ($results as $result) {
            // Format results as needed
            $formattedResults[] = [
                'maladie' => $result->getMaladie(),
                'description' => $result->getDescription(),
                'nbrVaccin' => $result->getNbrVaccin(),
                'dateVaccin' => $result->getDateVaccin(),
                'BloodType' => $result->getBloodType(),
                'sicknessEstimation' => $result->getSicknessEstimation(),
                // Add other fields as needed
            ];
        }
    
        return new JsonResponse($formattedResults);
    }



    
    #[Route('/new', name: 'app_info_medicaux_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $babyId = $request->query->get('babyId'); // Get baby id from the request query parameters
        
        // Find the Baby entity by its id
        $baby = $entityManager->getRepository(Baby::class)->find($babyId);
    
        // Create a new InfoMedicaux entity
        $infoMedicaux = new InfoMedicaux();
        
        // Set the Baby entity to the babyName property of InfoMedicaux
        $infoMedicaux->setBabyName($baby);
        
        // Create the form for InfoMedicaux entity
        $form = $this->createForm(InfoMedicauxType::class, $infoMedicaux);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Additional validation for dateVaccin
            $currentDate = new \DateTime();
            if ($infoMedicaux->getDateVaccin() > $currentDate) {
                $form->get('dateVaccin')->addError(new FormError('The vaccination date cannot be in the future.'));
                return $this->renderForm('info_medicaux/new.html.twig', [
                    'info_medicaux' => $infoMedicaux,
                    'form' => $form,
                ]);
            }
        
            // Persist the InfoMedicaux entity
            $entityManager->persist($infoMedicaux);
            $entityManager->flush();
        
            // Redirect to the index page or wherever you want
            return $this->redirectToRoute('app_info_medicaux_index', [], Response::HTTP_SEE_OTHER);
        }
        
        // Render the form template
        return $this->renderForm('info_medicaux/new.html.twig', [
            'info_medicaux' => $infoMedicaux,
            'form' => $form,
        ]);
    }
    
    

    #[Route('/{id}', name: 'app_info_medicaux_show', methods: ['GET'])]
    public function show(InfoMedicaux $infoMedicaux): Response
    {
        return $this->render('info_medicaux/show.html.twig', [
            'info_medicaux' => $infoMedicaux,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_info_medicaux_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InfoMedicaux $infoMedicaux, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InfoMedicauxType::class, $infoMedicaux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_info_medicaux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('info_medicaux/edit.html.twig', [
            'info_medicaux' => $infoMedicaux,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_info_medicaux_delete', methods: ['POST'])]
    public function delete(Request $request, InfoMedicaux $infoMedicaux, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$infoMedicaux->getId(), $request->request->get('_token'))) {
            $entityManager->remove($infoMedicaux);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_info_medicaux_index', [], Response::HTTP_SEE_OTHER);
    }
}
