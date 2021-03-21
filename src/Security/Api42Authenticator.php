<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserPhoto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;


class Api42Authenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $httpClient;
    private $router;

    public function __construct(EntityManagerInterface $em, HttpClientInterface $httpClient, RouterInterface $router)
    {
        $this->em = $em;
        $this->httpClient = $httpClient;
	    $this->router = $router;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'assessToken42';
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        return $request->query->get('assessToken');
        //var_dump($request->attributes->get('_route'));
        // var_dump($request->getPathInfo());
        // var_dump($request->server->get('HTTP_HOST'));
        // var_dump($request->cookies->get('PHPSESSID'));
        // var_dump($request->headers->get('content-type'));
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        //$credentials is accessToken hier
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }
        //?? what if no $response, the authentificationException take counte?
        $response = $this->httpClient->request("GET", "https://api.intra.42.fr/v2/me",[
             'headers' => [
                 "Authorization" => 'Bearer '.$credentials,
             ],
        ]);
        $content = $response->toArray();
        $id = $content['id'];
        //find if the user already logged once
        $existingUser = $this->em->getRepository(User::class)
        ->findOneBy(['id42' => $id]);
        if($existingUser)
            return $existingUser;
        $user = new User();
        $userPhoto = new UserPhoto();
        $email = $content['email'];
        $login = $content['login'];
        $firstName = $content['first_name'];
        $lastName = $content['last_name'];
        $userRole = $id === 50136 ? "ROLE_ADMIN" :"ROLE_USER";
        $avatar_url = $content['image_url'];

        $userPhoto->setPhotoLink($avatar_url);
        $userPhoto->setProfilePhoto(true);
        $userPhoto->setUser($user);

        $user->addUserPhoto($userPhoto);
        $user->setUsername($login);
        $user->setId42($id);
        $user->setEmail($email);
        $user->setFirstname($firstName);
        $user->setLastname($lastName);
        $user->setRoles($userRole);
        $user->setPassword('42loginNoPassword');
        $this->em->persist($userPhoto);
        $this->em->persist($user);
        $this->em->flush();
        // The "username" in this case is the apiToken, see the key `property`
        // of `your_db_provider` in `security.yaml`.
        // If this returns a user, checkCredentials() is called next:
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // Check credentials - e.g. make sure the password is valid.
        // In case of an API token, no credential check is needed.

        // Return `true` to cause authentication success
        if(!$user)
            return false;
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        $targetUrl = $this->router->generate('home');
        return new RedirectResponse($targetUrl);
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }

 
}
