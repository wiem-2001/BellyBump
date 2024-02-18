<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordUpdateFormType;
use App\Form\UpdateProfilFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController 
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/show_profil.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/admin/getAll_users', name: 'get_users')]
    public function getUsers(UserRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users=$repository->findAll();
        return $this->render('user/users.html.twig',array('users'=>$users));
    }

    #[Route('/user/delete/{id}', name: 'delete_user')]
    public function delete(ManagerRegistry $managerRegistry,$id,UserRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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
        $this->denyAccessUnlessGranted('ROLE_MOTHER');
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
    #[Route('/update-password', name: 'update_password')]
    public function updatePassword(Request $request,ManagerRegistry $managerRegistry,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MOTHER');
        // Get the current user
        $user = $this->getUser();

        // Handle the password update form submission
        $form = $this->createForm(PasswordUpdateFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the new password from the form
            // Update the user's password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // Persist the changes to the database
            $em= $managerRegistry->getManager();
            $em->flush();

            // Redirect the user after successful password update
            return $this->redirectToRoute('get_users');
        }

        // Render the password update form
        return $this->render('user/update_password.html.twig', [
            'passwordUpdateForm' => $form->createView(),
        ]);
    }

}
