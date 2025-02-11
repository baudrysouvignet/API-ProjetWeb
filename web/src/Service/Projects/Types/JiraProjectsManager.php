<?php

namespace App\Service\Projects\Types;


use App\Entity\JiraInfo;
use App\Entity\JiraProject;
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
}