<?php

namespace App\Controller\Projects;

use App\Entity\JiraProject;
use App\Entity\Project\TypesLignes;
use App\Repository\Project\TypesLignesRepository;
use App\Service\Projects\ProjectsManager;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/api/user/projects/{type}/{id}/set/lignes', name: 'app_projects_lignes_project')]
    public function set() {

    }
}
