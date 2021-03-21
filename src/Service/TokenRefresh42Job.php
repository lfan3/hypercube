<?php

namespace App\Service;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\Persistence\ObjectManager;


//normally i should write an interface non?
class TokenRefresh42Job
{
    private $om;
    //Injection independencency
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    } 

    public function refreshToken()
    {
        //todo
        //get the token which will be expired in 1h
        //update the access token, expired time and refresh token
        $httpClient = HttpClient::create();
        //to get from db
        $refreshToken = "2ca9f49ca0ef8919c6c3dda59b4f2bbaaaa7c800f7ad9a1c4e51220e74872706";
     
        $response = $httpClient->request('POST', 'https://api.intra.42.fr/oauth/token', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "client_id" => "29995395ad2e95e2ce60eb1705fa0667a22e40d81207d87f2e513035e1b418fb",
                "client_secret" => "81875d1efcbb93c743ae3aab34ece3927d74b53a8d01af16249a4a5adc2b8d9d",
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
        ]);
        $content = $response->toArray();
        var_dump($content);die;
        //content: array(6) { ["access_token"]=> string(64) "57c78548e45b4406d0a79b5328a4c1e9d11cb5914d639e416a89c9b037fa2655" ["token_type"]=> string(6) "bearer" ["expires_in"]=> int(4968) ["refresh_token"]=> string(64) "2ca9f49ca0ef8919c6c3dda59b4f2bbaaaa7c800f7ad9a1c4e51220e74872706" ["scope"]=> string(6) "public" ["created_at"]=> int(1612860871) }
        
    }
}