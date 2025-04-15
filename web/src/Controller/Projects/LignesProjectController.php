<?php

namespace App\Controller\Projects;

use App\Entity\JiraProject;
use App\Entity\Project\TypesLignes;
use App\Repository\Project\TypesLignesRepository;
use App\Service\Global\JsonValidator;
use App\Service\Projects\ProjectsManager;
use Doctrine\ORM\Mapping\Entity;
use JsonSchema\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class LignesProjectController extends AbstractController
{
    #[Route('/api/user/projects/{type}/{id}/get/lignes', name: 'app_projects_lignes_project')]
    public function index(
        int $id,
        string $type,
        ProjectsManager $projectsManager
    ): JsonResponse
    {
        $data = [
            'id' => $id,
            'type' => $type
        ];
        return $projectsManager->getLignes($data);
    }

    #[Route('/api/get/lignes/types', name: 'app_ptype_lignes_project')]
    public function gettypelignes(
        TypesLignesRepository $typesLignes
    ) : JsonResponse
    {
        $found = [];
        foreach ($typesLignes->findAll() as  $value) {
            $found[] = [
                'id' => $value->getId(),
                'name' => $value->getTitle()
            ];
        }

        return new JsonResponse([
            'code' => 200,
            'data' => $found
        ]);

    }

    #[Route('/api/user/projects/{type}/{id}/set/lignes', name: 'app_projects_set_lignes_project')]
    public function set(
        int $id,
        string $type,
        ProjectsManager $projectsManager,
        JsonValidator $validator,
        Request $request
    ): JsonResponse
    {
        $jsonSchema = json_decode('{
            "type": "object",
            "properties": {
                "ligne": {
                    "type": "array",
                    "minItems": 0,
                    "maxItems": 100,
                    "items": {
                        "type": "object",
                        "properties": {
                            "id_type_champs": { "type": "integer" },
                            "prompt": { "type": "string" }
                        },
                        "required": ["id_type_champs", "prompt"]
                    }
                }
            },
            "required": ["ligne"]
        }');

        $validate = $validator->validateJson(json_decode($request->getContent(), false), $jsonSchema);
        if ($validate) {
            return new JsonResponse([
                'code' => 400,
                'message' => $validate
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $data = json_decode($request->getContent(), true);
        return $projectsManager->setLignes($data, $id, $type);

    }
}
