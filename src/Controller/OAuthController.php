<?php

namespace App\Controller;
use App\Entity\User;
use App\Service;
use App\Service\OAuth42Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Persistence\ObjectManager;

class OAuthController extends AbstractController
{
    private $UID42;
    private $SECRET42;

    public function __construct($UID42, $SECRET42)
    {
        $this->UID42 = $UID42;
        $this->SECRET42 = $SECRET42;
    }
    /**
     * @Route("/oauth/check", name="o_auth_start")
     */
    public function gitHubEntry(ClientRegistry $clientRegistry): RedirectResponse
    {
        $client = $clientRegistry->getClient('github');
        return $client->redirect(['read:user','user:email']);
        // return $this->render('o_auth/index.html.twig', [
        //     'controller_name' => 'OAuthController',
        // ]);
    }
    /**
     * @Route("/oauth/check/github", name="o_auth_check")
     */
    public function checkGithub(ClientRegistry $clientRegistry)
    {
        
    }

  
    /**
     * from the 42btn, we direct to [oauth_get_42_code], 
     * then it will redirect to [oauth_get_42_token],
     * then redirect to [assessToken42]
     * then enter into my APi42Authenticator
     * @Route("/oauth/get42ApiCode", name="oauth_get_42_code")
     */
    public function get42ApiCode()
    {
        //https://api.intra.42.fr/oauth/authorize?client_id=29995395ad2e95e2ce60eb1705fa0667a22e40d81207d87f2e513035e1b418fb&redirect_uri=http%3A%2F%2Flocalhost%3A8000%2Foauth%2Fcheck%2F42Api&response_type=code

        $redirectUri = $this->generateUrl('oauth_get_42_token',[], UrlGeneratorInterface::ABSOLUTE_URL);//name of the route, not the path
        $httpQuery = http_build_query(array(
            
            "client_id" => $this->UID42,
            "client_secret" => $this->SECRET42,
            "redirect_uri" => $redirectUri,
            "scope" => "public",
            "response_type" => "code"
        ));
        $uri = "https://api.intra.42.fr/oauth/authorize?".$httpQuery;
        return $this->redirect($uri);
    }
    /**
     * @Route("/oauth/get42ApiToken", name="oauth_get_42_token")
     */
    public function getAccessToken(Request $request)
    {
        $code = $request->get('code');
        //if the user refused to authorise, go back to home
        if(!$code)
        {
            return $this->redirectToRoute("home");
        }
        $httpClient = HttpClient::create();
     
        $response = $httpClient->request('POST', 'https://api.intra.42.fr/oauth/token', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "grant_type" => "authorization_code",
                "client_id" => $this->UID42,
                "client_secret" => $this->SECRET42,
                "code" => $code,
                "redirect_uri" => "http://localhost:8000/oauth/get42ApiToken"
            ],
        ]);
        
        $content = $response->toArray();
       // return $this->redirectToRoute('assessToken42', ["assessToken42" => $content["access_token"]]);
        return $this->redirect("http://localhost:8000/oauth/check/42?assessToken=".$content["access_token"]);
    }

    /**
     * @Route("/oauth/check/42", name="assessToken42")
     */
    public function check42accessToken($assessToken42) 
    {
        //empty, this part is handled in Api42Authenticator

    }

}
