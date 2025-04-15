<?php

namespace App\Service\Projects;


use App\Entity\JiraProject;
use App\Entity\User;
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

    public function getProject(
        User $user
    ): array
    {
        return $this->em->getRepository(User::class)->getJiraProject($user);
    }
    public function getLignes(
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
                return new JsonResponse($service->getLignes($data['id']));
            }
        }
        return new JsonResponse([
            'code' => 400,
            'message' => 'Invalid project type'
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    public function addDescription(
        array $data,
        User $user
    )
    {
        $services = [];
        foreach (glob(__DIR__ . '/Types/*.php') as $file) {
            $services[] = basename($file, '.php');
        }

        foreach ($services as $service) {
            $service = 'App\Service\Projects\Types\\' . $service;
            $service = new $service($this->em);
            if ($service->isValidate($data['type'])) {
                return new JsonResponse($service->addDescription($data, $user));
            }
        }
        return new JsonResponse([
            'code' => 400,
            'message' => 'Invalid project type'
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    public function setLignes(array $data, int $id, string $type) {
        $services = [];
        foreach (glob(__DIR__ . '/Types/*.php') as $file) {
            $services[] = basename($file, '.php');
        }

        foreach ($services as $service) {
            $service = 'App\Service\Projects\Types\\' . $service;
            $service = new $service($this->em);
            if ($service->isValidate($type)) {
                return new JsonResponse($service->setLignes($data, $id));
            }
        }
        return new JsonResponse([
            'code' => 400,
            'message' => 'Invalid project type'
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}