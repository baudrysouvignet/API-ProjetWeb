<?php

namespace App\Tests\Controller\Projects;

use App\Controller\Projects\CreateProjectController;
use App\Service\Global\JsonValidator;
use App\Service\Projects\ProjectsManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CreateProjectControllerTest extends TestCase
{
    public function testCreateProjectValidRequest()
    {
        $jsonValidatorMock = $this->createMock(JsonValidator::class);
        $jsonValidatorMock->method('validateJson')->willReturn(null);

        $projectsManagerMock = $this->createMock(ProjectsManager::class);
        $projectsManagerMock->method('createProject')->willReturn(new JsonResponse([
            'code' => 201,
            'message' => 'Project created successfully'
        ], JsonResponse::HTTP_CREATED));

        $controller = new CreateProjectController();
        $request = new Request([], [], [], [], [], [], json_encode(['type' => 'Software']));
        $response = $controller->index($jsonValidatorMock, $request, $projectsManagerMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 201, 'message' => 'Project created successfully']),
            $response->getContent()
        );
    }

    public function testCreateProjectInvalidJson()
    {
        $jsonValidatorMock = $this->createMock(JsonValidator::class);
        $jsonValidatorMock->method('validateJson')->willReturn('Invalid JSON format');

        $projectsManagerMock = $this->createMock(ProjectsManager::class);
        $controller = new CreateProjectController();
        $request = new Request([], [], [], [], [], [], json_encode(['wrongField' => 'value']));
        $response = $controller->index($jsonValidatorMock, $request, $projectsManagerMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 400, 'message' => 'Invalid JSON format']),
            $response->getContent()
        );
    }
}
