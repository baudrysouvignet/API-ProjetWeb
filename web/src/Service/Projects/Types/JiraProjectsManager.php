<?php

namespace App\Service\Projects\Types;


use App\Entity\JiraInfo;
use App\Entity\JiraProject;
use App\Entity\Project\Lignes;
use App\Entity\User;
use App\Repository\JiraProjectRepository;
use App\Service\Platforms\JiraInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class JiraProjectsManager
{
    private $em;
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }
    public function isValidate(
        string $type,
    ): bool
    {
        return $type === 'jira';
    }

    public function createProject(
        array $data
    ): JsonResponse
    {
        return $this->em->getRepository(JiraProject::class)->createProject(
            $this->em->getRepository(JiraInfo::class)->findOneBy(["id" => $data['info']['account']]),
            $data['info']['id'],
            $data['info']['issues']
        );
    }

    public function addDescription(
        array $data,
        User $user
    ): JsonResponse
    {
        $project = $this->em->getRepository(JiraProject::class)->findOneBy(
            ['id' => $data['id']]
        );
        if (!$project || $project->getJiraInfo()->getUser() !== $user){
            return new JsonResponse([
                'code' => 400,
                'message' => 'Invalid project'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $project->setDescription($data['description']);
        $this->em->flush();
        return new JsonResponse([
            'code' => 200,
            'message' => 'Description added'
        ], JsonResponse::HTTP_OK);
    }

    public function getLignes(int $id): array{

        $project = $this->em->getRepository(JiraProject::class)->findOneBy(
            ['id' => $id]
        );
        if (!$project){
            return [
                'code' => 400,
                'message' => 'Invalid project'
            ];
        }

        $lignes = $this->em->getRepository(Lignes::class)->findBy(
            ['project' => $id]
        );
        $result = [];
        foreach ($lignes as $ligne){
            $result[] = [
                'id' => $ligne->getId(),
                'prompt' => $ligne->getPrompt(),
                'issueTypes' => $ligne->getType()->getTitle(),
                'issueTypesId' => $ligne->getType()->getId(),
            ];
        }
        return $result;
    }

    public function setLignes($data, $id)
    {
       $this->em->getRepository(JiraProject::class)->addNewLignes($data, $id);
       return [
            'code' => 200,
            'message' => 'Lignes added'
        ];
    }
}