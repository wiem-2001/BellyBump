<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateProfilFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/show_profil.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/getAll_users', name: 'get_users')]
    public function getUsers(UserRepository $repository): Response
    {
        $users=$repository->findAll();
        return $this->render('user/users.html.twig',array('users'=>$users));
    }

    #[Route('/user/delete/{id}', name: 'delete_user')]
    public function delete(ManagerRegistry $managerRegistry,$id,UserRepository $repository): Response
    {
        $user= $repository->find($id);
        $em= $managerRegistry->getManager();
        if(in_array('ROLE_MOTHER', $user->getRoles(), true)){
            $em->remove($user);
            $em->flush();
        }
        else{
            return new Response("Error");
        }
        return $this->redirectToRoute('get_users');
    }
    #[Route('/user/details/{id}', name: 'detail_user')]
    public function showProfil($id, UserRepository $repository): Response
    {
        $user = $repository->find($id);
        $form = $this->createForm(UpdateProfilFormType::class, $user);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user/show_profil.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

}
