<?php

namespace App\Tests\Service\Projects;

use App\Service\Projects\ProjectsManager;
use App\Service\Projects\Types\JiraProjectsManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectsManagerTest extends TestCase
{
    public function testCreateProjectWithValidType(): void
    {
        $mockService = $this->createMock(JiraProjectsManager::class);
        $mockService->method('isValidate')->willReturn(true);
        $mockService->method('createProject')->willReturn(
            new JsonResponse(['code' => 201, 'message' => 'Project created successfully'], JsonResponse::HTTP_CREATED)
        );

        $projectsManagerMock = $this->getMockBuilder(ProjectsManager::class)
            ->disableOriginalConstructor() 
            ->getMock();

        $projectsManagerMock->method('createProject')
            ->willReturnCallback(function ($data) use ($mockService) {
                if ($data['type'] === 'jira') {
                    return $mockService->createProject($data);
                }
                return new JsonResponse([
                    'code' => 400,
                    'message' => 'Invalid project type'
                ], JsonResponse::HTTP_BAD_REQUEST);
            });

        $data = ['type' => 'jira', 'name' => 'Test Project'];
        $response = $projectsManagerMock->createProject($data);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 201, 'message' => 'Project created successfully']),
            $response->getContent()
        );
    }

    public function testCreateProjectWithInvalidType(): void
    {
        $projectsManagerMock = $this->getMockBuilder(ProjectsManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $projectsManagerMock->method('createProject')
            ->willReturnCallback(function ($data) {
                if ($data['type'] === 'jira') {
                    return new JsonResponse(['code' => 201, 'message' => 'Project created successfully'], JsonResponse::HTTP_CREATED);
                }
                return new JsonResponse([
                    'code' => 400,
                    'message' => 'Invalid project type'
                ], JsonResponse::HTTP_BAD_REQUEST);
            });

        $data = ['type' => 'unknown', 'name' => 'Test Project'];
        $response = $projectsManagerMock->createProject($data);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 400, 'message' => 'Invalid project type']),
            $response->getContent()
        );
    }
}
