<?php

namespace App\Controller\Platforms;

use App\Service\Global\JsonValidator;
use App\Service\Platforms\JiraLogin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class JiraLoginController extends AbstractController
{
    #[Route('/api/user/platforms/jira/add', name: 'app_platforms_jira_login', methods: ['POST'])]
    public function index(
        JsonValidator $validator,
        Request $request,
        JiraLogin $JiraLogin
    ): JsonResponse
    {
        $jsonSchema = json_decode('{
            "type": "object",
            "properties": {
                "url": {"type": "string"},
                "emailJira": {"type": "string", "format": "email"},
                "token": {"type": "string"}
            },
            "required": ["url", "emailJira", "token"]
        }');
        $validate = $validator->validateJson(json_decode($request->getContent(), false), $jsonSchema);

        if ($validate) {
            return new JsonResponse([
                'code' => 400,
                'message' => $validate
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        return $JiraLogin->connect(
            $data['emailJira'],
            $data['url'],
            $data['token']
        );
    }
}
