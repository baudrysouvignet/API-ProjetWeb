<?php

namespace App\Service\Projects;


use App\Service\Projects\Types\JiraProjectsManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectsManager
{
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
            $service = new $service();
            if ($service->isValidate($data['type'])) {
                return $service->createProject($data);
            }
        }
        return new JsonResponse([
            'code' => 400,
            'message' => 'Invalid project type'
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}