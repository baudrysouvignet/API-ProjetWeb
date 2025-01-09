<?php

namespace App\Controller\Platforms;

use App\Service\Platforms\PlatfomrsServcie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class PlatformsController extends AbstractController
{
    #[Route('/api/user/platforms', name: 'app_platforms_platforms', methods: ['GET'])]
    public function index(
        PlatfomrsServcie $platformsService
    ): JsonResponse
    {
        return new JsonResponse([
            'code' => 200,
            'data' => $platformsService->getPlatforms(
                $this->getUser()
            )
        ]);
    }
}
