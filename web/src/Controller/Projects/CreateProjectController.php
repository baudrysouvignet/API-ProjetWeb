<?php

namespace App\Controller\Projects;

use App\Service\Global\JsonValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CreateProjectController extends AbstractController
{
    #[Route('/api/user/projects/create/', name: 'app_projects_create_project')]
    public function index(
        JsonValidator $validator,
        Request $request,
    ): JsonResponse
    {
        $jsonSchema = json_decode('{
            "type": "object",
            "properties": {
                "type": {"type": "string"},
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



        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Projects/CreateProjectController.php',
        ]);
    }
}
