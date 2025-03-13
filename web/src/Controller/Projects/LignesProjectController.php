<?php

namespace App\Controller\Projects;

use App\Entity\JiraProject;
use App\Service\Projects\ProjectsManager;
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
}
