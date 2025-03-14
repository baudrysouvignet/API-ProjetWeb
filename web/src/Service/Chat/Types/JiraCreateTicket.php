<?php

namespace App\Service\Chat\Types;

use App\Entity\JiraInfo;
use App\Entity\JiraProject;
use App\Service\Global\Cryptage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class JiraCreateTicket
{
    private $em;

    public function __construct(
        EntityManagerInterface $em,
    ) {
        $this->em = $em;
    }

    public function isValidate(
        string $type,
    ): bool
    {
        return $type === 'jira';
    }

    public function create(
        int $id,
        array $data
    )
    {
        $project = $this->getProject($id);
        if (!$project) {
            return new JsonResponse([
                'code' => 400,
                'message' => 'Project not found'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $projectInfo = $this->getProljectInfo($id);
        if ($projectInfo == []) {
            return new JsonResponse([
                'code' => 400,
                'message' => 'Project info not found'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $token = $this->em->getRepository(JiraInfo::class)->decrypteToken($project->getJiraInfo());
        $url = $project->getJiraInfo()->getUrl();
        $this->sendRequest($url, $token, $data, $projectInfo);
        return new JsonResponse([
            'code' => 200,
            'message' => 'Ticket created'
        ], JsonResponse::HTTP_OK);
    }

    private function sendRequest(
        string $url,
        string $token,
        array $data,
        array $params
    )
    {
        $this->em->getRepository(JiraProject::class)->createTicket($url, $token, $data, $params);
    }

    private function getProject(int $id): JiraProject|null
    {
        return $this->em->getRepository(JiraProject::class)->findOneBy(['id' => $id]);
    }

    private function getProljectInfo(int $id): array
    {
        return $this->em->getRepository(JiraProject::class)->findDataForTicket($this->getProject($id));
    }

    public function get(
        int $id
    )
    {
        $project = $this->em->getRepository(JiraProject::class)->find([
            'id' => $id
        ]);
        if (!$project) {
            return [];
        }
        $lignes = [];
        foreach ($project->getLignes() as $ligne) {
            $lignes[$ligne->getType()->getTitle()] = [
                'prompt' => $ligne->getPrompt()
            ];
        }
        $lignes["priority"] = ["isAdmin"=> True];
        return $lignes;
    }
}
