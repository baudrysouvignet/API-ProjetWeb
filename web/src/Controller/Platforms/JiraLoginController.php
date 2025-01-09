<?php

namespace App\Controller\Platforms;

use App\Entity\JiraInfo;
use App\Repository\JiraInfoRepository;
use App\Service\Global\JsonValidator;
use App\Service\Platforms\JiraLogin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class JiraLoginController extends AbstractController
{
    #[Route('/api/user/platforms/jira/add', name: 'app_platforms_jira_add', methods: ['POST'])]
    public function index(
        JsonValidator $validator,
        Request $request,
        JiraLogin $JiraLogin
    ): JsonResponse
    {
        $jsonSchema = json_decode('{
            "type": "object",
            "properties": {
                "url": {"type": "string"},
                "emailJira": {"type": "string", "format": "email"},
                "token": {"type": "string"}
            },
            "required": ["url", "emailJira", "token"]
        }');
        $validate = $validator->validateJson(json_decode($request->getContent(), false), $jsonSchema);

        if ($validate) {
            return new JsonResponse([
                'code' => 400,
                'message' => $validate
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        return $JiraLogin->connect(
            $data['emailJira'],
            $data['url'],
            $data['token']
        );
    }

    #[Route('/api/user/platforms/jira/delete/{id}', name: 'app_platforms_jira_delete', methods: ['POST'])]
    public function delete(
        JiraInfo $JiraInfo,
        JiraInfoRepository $JiraInfoRepository
    ): JsonResponse
    {
        if ($JiraInfo->getUser() !== $this->getUser()) {
            return new JsonResponse([
                'code' => 400,
                'message' => 'You are not allowed to delete this Jira account'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $JiraInfoRepository->deleteJiraInfo($JiraInfo);

        return new JsonResponse([
            'code' => 200,
            'message' => 'Jira account deleted'
        ], JsonResponse::HTTP_OK);
    }
}
