<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * this controller contains some basique repeating functions in several controller
 */
class BaseController extends AbstractController
{

    protected function createApiResponse($data, $statusCode = 200)
    {
        if($statusCode == 200)
            $response = new JsonResponse(['data'=>$data], $statusCode);
        else
            $response = new JsonResponse(['error'=>$data], $statusCode);

        return $response;
    }


}
