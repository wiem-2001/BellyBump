<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PartnerController extends AbstractController
{
    #[Route('/partner', name: 'app_partner')]
    public function index(): Response
    {
        $partner=$this->getDoctrine()->getRepository(partenaire::class)->findAll();


        return $this->render('partner/index.html.twig', [
            'p'=>$partner
        ]);
    }
    #[Route('/partnerback', name: 'back_partner')]
    public function indexback(): Response
    {
        $partner=$this->getDoctrine()->getRepository(partenaire::class)->findAll();


        return $this->render('partner/showPartner.html.twig', [
            'p'=>$partner
        ]);
    }


    #[Route('/addpartner', name: 'add_partner')]
    public function addpartner(Request $request, ManagerRegistry $managerRegistry,Security $security): Response
    {
        $user = $this->getUser();
        $partner = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            $em->persist($partner);
            $em->flush();

            // Add a flash message
            $this->addFlash(
                'success',
                'Add with success'
            );

            // Render the same page to stay on it
            return $this->renderForm("partner/addpartner.html.twig", [
                'fpartner' => $form,
                'user'=>$user
            ]);
        }

        return $this->renderForm("partner/addpartner.html.twig", [
            'fpartner' => $form,
        ]);
    }




    #[Route('/remove/{id}', name: 'remove_partner')]

    public function delete(ManagerRegistry $managerRegistry,PartenaireRepository $repository,$id)
    {
        $partner= $repository->find($id);
        $em= $managerRegistry->getManager();

            $em->remove($partner);
            $em->flush();


        return $this->redirectToRoute("back_partner");

    }


    #[Route('/updatepartner/{id}', name: 'update_partner')]
    public function updatepartner(Request $request,ManagerRegistry $managerRegistry,$id,partenaireRepository $repository,Security $security): Response
    {   $user=$security->getUser();
        $partner = $this->getDoctrine()->getManager()->getRepository(partenaire::class)->find($id);
        $form = $this->createForm(partenaireType::class, $partner);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isvalid()) {
            $em = $managerRegistry->getManager();
            //$em->persist($partner);
            $em->flush();
            return $this->redirectToRoute("back_partner");
        }

        return $this->renderForm("partner/update.html.twig"
            ,array('fpartner'=>$form , 'user'=>$user));

    }

    #[Route('/partners/search', name: 'partner_search')]
    public function search(Request $request)
    {
        $searchTerm = $request->query->get('search');
        $partners = $this->getDoctrine()->getRepository(Partenaire::class)->findBySearchTerm($searchTerm);

        return $this->render('partner/showPartner.html.twig', [
            'p' => $partners
        ]);
    }
}
