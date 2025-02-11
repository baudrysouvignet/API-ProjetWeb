<?php

namespace App\Service\Projects;


use App\Entity\JiraProject;
use App\Service\Projects\Types\JiraProjectsManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectsManager
{
    private $em;
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    public function createProject(
        array $data
    ): JsonResponse
    {
        $services = [];
        foreach (glob(__DIR__ . '/Types/*.php') as $file) {
            $services[] = basename($file, '.php');
        }

        foreach ($services as $service) {
            $service = 'App\Service\Projects\Types\\' . $service;
            $service = new $service($this->em);
            if ($service->isValidate($data['type'])) {
                return $service->createProject($data);
            }
        }
        return new JsonResponse([
            'code' => 400,
            'message' => 'Invalid project type'
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    public function getProject(): array
    {
        $result = [];
        foreach ($this->em->getRepository(JiraProject::class)->findAll() as $value) {
            $result [] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
                'type'=> 'jira',
            ];
        }
        return $result;
    }
}