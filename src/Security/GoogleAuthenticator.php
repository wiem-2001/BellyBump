<?php
namespace App\Security;

use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        // Check if the current ROUTE matches the check ROUTE for Google
        return $request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET');
    }

    public function getCredentials(Request $request)
    {
        // Fetch the access token from Google
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Fetch the user information from Google
        $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();

        // Check if the user already exists in the database by their email
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($existingUser) {
            return $existingUser;
        }

        // If the user does not exist, create a new user
        $user = new User();
        $user->setEmail($email); // Assuming you have setEmail() method in your User entity
        // Set other properties as needed

        // Persist and flush the new user
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Returns the Google client.
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // Redirect the user to the homepage after successful authentication
        $targetUrl = $this->router->generate('app_test');
        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // Handle authentication failure, you can customize the response as needed
        return new Response('Authentication failed', Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // Redirect to the login page if authentication is needed
        return new RedirectResponse('/');
    }
}
