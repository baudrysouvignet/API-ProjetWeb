<?php

namespace App\Controller\Chat;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SendDataController extends AbstractController
{
    #[Route('/user/chat/{id}', name: 'app_chat_send_data')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'finalResponse' => [
                "title"=>"string",
                "description"=>"string",
                "subtitle"=>"string",
                "priority"=>"string"
            ],
            'responses' => [
                "message"=>"string"
            ],
            'client' => [
                "title"=>["prompt"=> "une description Simple du probléme"],
                "description"=>["prompt"=> "Je veux connaitre la page, le téme du navigateur (clair/sombre) et la derniére action qu'il a fait sur le site"],
                "priority"=>["isAdmin"=> True]
            ]
        ]);
    }
}
