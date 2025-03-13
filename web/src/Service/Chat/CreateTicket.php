<?php

namespace App\Service\Chat;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateTicket
{
    private $em;
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    public function createTicket(
        string $platform,
        array $data,
        int $projectId
    ): JsonResponse
    {
        $services = [];
        foreach (glob(__DIR__ . '/Types/*.php') as $file) {
            $services[] = basename($file, '.php');
        }

        foreach ($services as $service) {
            $service = 'App\Service\Chat\Types\\' . $service;
            $service = new $service($this->em);
            if ($service->isValidate($platform)) {
                return $service->create($projectId, $data);
            }
        }
        return new JsonResponse([
            'code' => 400,
            'message' => 'Invalid project type'
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}