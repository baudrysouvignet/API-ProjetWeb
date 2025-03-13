<?php

namespace App\Controller\Chat;

use App\Service\Chat\CreateTicket;
use App\Service\Global\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SendDataController extends AbstractController
{
    #[Route('/api/chat/{id}', name: 'app_chat_send_data')]
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



    #[Route('/api/chat/create/{platform}/{id}', name: 'app_chat_create_ticket')]
    public function createProject(
        Request $request,
        JsonValidator $validator,
        string $platform,
        int $id,
        CreateTicket $createTicket
    ): JsonResponse
    {
        $jsonSchema = json_decode('{
            "type": "object",
            "properties": {
                "title": {"type": "string"},
                "description": {"type": "string"},
                "priority": {"type": "string"}
            },
            "required": ["title", "description", "priority"]
        }');

        $validate = $validator->validateJson(json_decode($request->getContent(), false), $jsonSchema);


        if ($validate) {
            return new JsonResponse([
                'code' => 400,
                'message' => $validate
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        return $createTicket->createTicket($platform, $data, $id);
    }
}
