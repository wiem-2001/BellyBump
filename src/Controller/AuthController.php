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
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        $user = $security->getUser();
        if ($user) {
            // Check the role of the user
            if (in_array('ROLE_MOTHER', $user->getRoles())) {
                $isVerified=$user->getIsVerified();
                dump($isVerified);
                if($isVerified==1){
                    return $this->redirectToRoute('app_testTemplate');
                }
            }
            else{
                return $this->redirectToRoute('get_users');
            }
        }

        // Handle login form rendering
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/mother_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
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
    #[Route('/forgetPassword', name: 'app_forgotPassword')]
    public function forgetPassword(Request $request, UserRepository $usersRepository, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager): Response
      {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);
   if ($form->isSubmitted() && $form->isValid()) {
            //on va chercher l'utilisateur par son email
            $user=$usersRepository->findOneBy(['email'=>$form->get('email')->getData()]);
            dump($user);
            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $context = compact('url', 'user');
                $mail = (new TemplatedEmail())
                    ->from(new Address('bellybump4@gmail.com', 'reset_password'))
                    ->to($user->getEmail() )
                    ->subject('Reset your password')
                    ->htmlTemplate("Security/resetPassword.html.twig")
                    ->context($context);

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }
    #[Route('/resetPassword', name: 'reset_password')]
    public function resetPassword(): void
    {

    }

}
