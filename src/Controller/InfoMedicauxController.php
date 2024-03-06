<?php

namespace App\Controller;

use App\Entity\InfoMedicaux;
use App\Entity\Baby;
use App\Entity\Med  ;

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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

#[Route('/info/medicaux')]
class InfoMedicauxController extends AbstractController
{

    #[Route('/stats',name: 'app_stat', methods: ['GET'])]
   
public
function
statistics(InfoMedicauxRepository $infoMedicauxRepository):Response
    {
       
$repository =
$this->getDoctrine()->getRepository(InfoMedicaux::class);


       
$data =
$repository->createQueryBuilder('v')
    ->select('v.BloodType')
    ->addSelect('COUNT(v.id)
 as totalBloodType')
    ->addSelect('SUM(CASE
 WHEN v.BloodType = :BMinus THEN 1 ELSE 0 END) as bCount')
    ->addSelect('SUM(CASE
 WHEN v.BloodType = :BPlus THEN 1 ELSE 0 END) as bbCount')
    ->addSelect('SUM(CASE
 WHEN v.BloodType = :OMinus THEN 1 ELSE 0 END) as oCount')
    ->addSelect('SUM(CASE
 WHEN v.BloodType = :OPlus THEN 1 ELSE 0 END) as ooCount')
    ->addSelect('SUM(CASE
 WHEN v.BloodType = :AMinus THEN 1 ELSE 0 END) as aCount')
    ->addSelect('SUM(CASE
 WHEN v.BloodType = :APlus THEN 1 ELSE 0 END) as aaCount')
    ->addSelect('SUM(CASE
 WHEN v.BloodType = :ABMinus THEN 1 ELSE 0 END) as abCount')
    ->addSelect('SUM(CASE
 WHEN v.BloodType = :ABPlus THEN 1 ELSE 0 END) as abbCount')
    ->setParameter('BMinus',
'B-')
    ->setParameter('BPlus',
'B+')
    ->setParameter('OMinus',
'O-')
    ->setParameter('OPlus',
'O+')
    ->setParameter('AMinus',
'A-')
    ->setParameter('APlus',
'A+')
    ->setParameter('ABMinus',
'AB-')
    ->setParameter('ABPlus',
'AB+')
    ->groupBy('v.BloodType')
    ->getQuery()
    ->getResult();


   
 


       
return
$this->render('info_medicaux/chart.html.twig',
 [
           
'data' =>
$data,
        ]);
    }


    #[Route('/', name: 'app_info_medicaux_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, InfoMedicauxRepository $infoMedicauxRepository,Security $security): Response
    {
        $user=$security->getUser();

    $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $queryBuilder = $infoMedicauxRepository->createQueryBuilder('i');
        $queryBuilder->leftJoin('i.med', 'm'); // Assuming 'med' is the association in InfoMedicaux entity
    
        // Paginate the query
        $pagination = $paginator->paginate(
            $queryBuilder, // Query to paginate
            $request->query->getInt('page', 1), // Current page number
            4 // Number of items per page
        );
    
        return $this->render('info_medicaux/index.html.twig', [
            'pagination' => $pagination,
            'user'=>$user

        ]);
    }
    
    

    #[Route('/frontInfo', name: 'app_frontInfo_index', methods: ['GET'])]
    public function indexxx(InfoMedicauxRepository $infoMedicauxRepository,Security $security): Response
    {
        
        $user=$security->getUser();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('info_medicaux\frontInfo.html.twig', [
            'info_medicauxes' => $infoMedicauxRepository->findAll(),
            'user'=>$user
        ]);
    }

    #[Route('/search', name: 'app_info_medicaux_search', methods: ['GET'])]
    public function search(Request $request, InfoMedicauxRepository $infoMedicauxRepository,Security $security): JsonResponse
    {
        $user=$security->getUser();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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
            'user'=>$user

                // Add other fields as needed
            ];
        }
    
        return new JsonResponse($formattedResults);
    }



    
    #[Route('/new', name: 'app_info_medicaux_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,Security $security): Response
    {
        $user=$security->getUser();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $babyId = $request->query->get('babyId');
        $baby = $entityManager->getRepository(Baby::class)->find($babyId);
    
        $infoMedicaux = new InfoMedicaux();
        $infoMedicaux->setBabyName($baby);
    
        $form = $this->createForm(InfoMedicauxType::class, $infoMedicaux);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Fetch the managed Med entity from the EntityManager
            $medId = $infoMedicaux->getMed()->getId();
            $med = $entityManager->getRepository(Med::class)->find($medId);
            
            // Set the managed Med entity to the InfoMedicaux entity
            $infoMedicaux->setMed($med);
    
            $entityManager->persist($infoMedicaux);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_info_medicaux_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('info_medicaux/new.html.twig', [
            'info_medicaux' => $infoMedicaux,
            'form' => $form,
            'user'=>$user

        ]);
    }
    
    
    

    #[Route('/{id}', name: 'app_info_medicaux_show', methods: ['GET'])]
    public function show(InfoMedicaux $infoMedicaux,Security $security): Response
    {
        $user=$security->getUser();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('info_medicaux/show.html.twig', [
            'info_medicaux' => $infoMedicaux,
            'user'=>$user

        ]);
    }

    #[Route('/{id}/edit', name: 'app_info_medicaux_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InfoMedicaux $infoMedicaux, EntityManagerInterface $entityManager,Security $security): Response
    {
        $user=$security->getUser();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(InfoMedicauxType::class, $infoMedicaux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_info_medicaux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('info_medicaux/edit.html.twig', [
            'info_medicaux' => $infoMedicaux,
            'form' => $form,
            'user'=>$user

        ]);
    }

    #[Route('/{id}', name: 'app_info_medicaux_delete', methods: ['POST'])]
    public function delete(Request $request, InfoMedicaux $infoMedicaux, EntityManagerInterface $entityManager,Security $security): Response
    {
        $user=$security->getUser();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$infoMedicaux->getId(), $request->request->get('_token'))) {
            $entityManager->remove($infoMedicaux);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_info_medicaux_index', ['user'=>$user], Response::HTTP_SEE_OTHER);
    }
}
