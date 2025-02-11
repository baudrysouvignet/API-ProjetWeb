<?php

namespace App\Controller\Projects;

use App\Service\Global\JsonValidator;
use App\Service\Projects\ProjectsManager;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CreateProjectController extends AbstractController
{
    #[Route('/api/user/projects/create', name: 'app_projects_create_project', methods: ['POST'])]
    public function index(
        JsonValidator $validator,
        Request $request,
        ProjectsManager $projectsManager
    ): JsonResponse
    {
        $jsonSchema = json_decode('{
            "type": "object",
            "properties": {
                "type": {"type": "string"},
                "title": {"type": "string"},
                "info": {
                    "type": "object",
                    "properties": {
                        "account": {"type": "integer"},
                        "id": {"type": "integer"},
                        "issues": {"type": "integer"}
                    },
                    "required": ["account", "id", "issues"]
                }
            },
            "required": ["type", "title", "info"]
        }');

        $validate = $validator->validateJson(json_decode($request->getContent(), false), $jsonSchema);


        if ($validate) {
            return new JsonResponse([
                'code' => 400,
                'message' => $validate
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $data = json_decode($request->getContent(), true);

        return $projectsManager->createProject(
            $data
        );
    }

    #[Route('/api/user/projects/get', name: 'app_projects_get_project', methods: ['GET'])]
    public function get(
        ProjectsManager $projectsManager
    ): JsonResponse
    {
        return new JsonResponse($projectsManager->getProject());
    }
}
