<?php

namespace App\Controller;

use App\Form\PasswordUpdateFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
class AuthController extends AbstractController
{
    #[Route('/loginAdmin', name: 'admin_login')]
    public function loginAdmin(AuthenticationUtils $authenticationUtils,Security $security): Response
    {

        $user = $security->getUser();
        if ($user) {
            // Check the role of the user
            if (in_array('ROLE_MOTHER', $user->getRoles())) {
                // If the user is a mother, redirect to another route
                return $this->redirectToRoute('app_testTemplate');
            }
           else{
            return $this->redirectToRoute('get_users');
           }
        }
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/admin_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route('/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils,Security $security): Response
    {
        $user = $security->getUser();
        if ($user) {
            // Check the role of the user
            if (in_array('ROLE_MOTHER', $user->getRoles())) {
                // If the user is a mother, redirect to another route
                return $this->redirectToRoute('app_testTemplate');
            }
            else{
                return $this->redirectToRoute('get_users');
            }
        }
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/mother_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route('/logoutAdmin', name: 'admin_logout')]
    public function logoutAdmin(): void
    {

    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {

    }




}
