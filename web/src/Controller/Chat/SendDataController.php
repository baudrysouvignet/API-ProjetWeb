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
    #[Route('/api/chat/{platform}/{id}', name: 'app_chat_send_data')]
    public function index(
        Request $request,
        JsonValidator $validator,
        string $platform,
        int $id,
        CreateTicket $createTicket
    ): JsonResponse
    {
        $tickets = $createTicket->getTicket($platform, $id);
        $finalResponse = [];
        foreach ($tickets as $key => $value) {
            $finalResponse[$key] = "string";
        }

        return new JsonResponse([
            'responses' => [
                "message"=>"string"
            ],
            'client' => [
                ... $tickets
            ],
            'finalResponse' => [
                ... $finalResponse
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
