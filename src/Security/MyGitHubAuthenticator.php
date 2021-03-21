<?php
namespace App\Security;

use App\Entity\User; // your user entity
use App\Entity\UserPhoto;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\GithubClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


class MyGitHubAuthenticator extends SocialAuthenticator
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
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'o_auth_check';
    }

    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        // For Symfony lower than 3.4 the supports method need to be called manually here:
        // if (!$this->supports($request)) {
        //     return null;
        // }
        // get the user directly
     
        return $this->fetchAccessToken($this->getGithubClient());
    }
    

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
      
        /** @var GithubUser $githubUser */
        $githubUser = $this->getGithubClient()
            ->fetchUserFromToken($credentials);

        $email = $githubUser->getEmail();
        // 1) have they logged in with Github before? Easy!
        $existingUser = $this->em->getRepository(User::class)
            ->findOneBy(['githubId' => $githubUser->getId()]);
        if ($existingUser) {
            return $existingUser;
        }

        // 2) do we have a matching user by email?
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $email]);
        // 3) Maybe you just want to "register" them by creating
        // if the email has no matching, create a new User object
        if(!$user){
            $user = new User();
            $userPhoto = new UserPhoto();
            $userInfo = $githubUser->toArray();

            $userPhoto->setPhotoLink($userInfo['avatar_url']);
            $userPhoto->setProfilePhoto(true);
            $userPhoto->setUser($user);

            $user->setUsername($userInfo['login']);
            $user->setGithubId($userInfo['id']);
            $user->setPassword('githubloginNoPassword');
            $user->setRoles('User');
            if(!$userInfo['email'])
                $user->setEmail('github mail is not available');
            else
                $user->setEmail($userInfo['email']);
        }
        //if the email existes already, then juste give this user his githubId
        $user->setGithubId($githubUser->getId());
        $this->em->persist($userPhoto);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @return GithubClient
     */
    private function getGithubClient()
    {
        return $this->clientRegistry
            // "github_main" is the key used in config/packages/knpu_oauth2_client.yaml
            ->getClient('github');
	}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('home');
        return new RedirectResponse($targetUrl);
    
        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/login/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    // ...
}
