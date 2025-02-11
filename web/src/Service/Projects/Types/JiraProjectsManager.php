<?php

namespace App\Service\Projects\Types;


use Symfony\Component\HttpFoundation\JsonResponse;

class JiraProjectsManager
{
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
        //dd($data);
        return new JsonResponse([
            'code' => 200,
            'message' => 'Jira project created'
        ]);
    }
}