<?php

// src/Security/LogoutSuccessHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Security;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $urlGenerator;
    private $security;

    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    public function onLogoutSuccess(Request $request)
    {
       // $targetUrl = $this->urlGenerator->generate('app_login'); // Default to mother login page

        // Get the current user
        $user = $this->security->getUser();

        // Check if the user has a role and set the target URL accordingly
        if (in_array('ROLE_MOTHER', $user->getRoles())) {
            $targetUrl = $this->urlGenerator->generate('app_login');
        } elseif (in_array('ROLE_ADMIN', $user->getRoles())) {
            $targetUrl = $this->urlGenerator->generate('admin_login');
        }

        return new RedirectResponse($targetUrl);
    }
}