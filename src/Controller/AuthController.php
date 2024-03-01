<?php

namespace App\Controller;

use App\Form\PasswordUpdateFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Security\SendMail;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class AuthController extends AbstractController
{
    #[Route('/admin/loginAdmin', name: 'admin_login')]
    public function loginAdmin(AuthenticationUtils $authenticationUtils, Security $security): Response
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

        // Handle login form rendering
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/admin_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security, SessionInterface $session): Response
    {

        $user = $security->getUser();
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($user) {
            // Check the role of the user
            if (in_array('ROLE_MOTHER', $user->getRoles())) {
                $isVerified = $user->getIsVerified();
                if ($isVerified == 0 && $user->isStatus() == 0) {
                    $this->addFlash('error', 'Your account is not verified. Please verify your account.');
                }
                elseif ($isVerified == 1 && $user->isStatus() == 0) {
                    $this->addFlash('error', 'Your account is inactive. Please contact support.');
                    $this->redirectToRoute('app_logout');
                }
                else{
                    return $this->redirectToRoute('app_testTemplate');
                }
            } else {
                return $this->redirectToRoute('get_users');
            }
        }elseif($error){
            $this->addFlash('error', 'Invalid credentials. Please try again.');

        }

        // Handle login form rendering
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/mother_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/admin/logoutAdmin', name: 'admin_logout')]
    public function logoutAdmin(): void
    {

    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {

    }

}
