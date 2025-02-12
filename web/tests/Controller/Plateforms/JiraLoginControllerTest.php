<?php

namespace App\Tests\Controller\Platforms;

use App\Controller\Platforms\JiraLoginController;
use App\Entity\JiraInfo;
use App\Repository\JiraInfoRepository;
use App\Service\Global\JsonValidator;
use App\Service\Platforms\JiraLogin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Response;

class JiraLoginControllerTest extends TestCase
{
    public function testAddJiraAccount()
    {
        $jsonValidatorMock = $this->createMock(JsonValidator::class);
        $jsonValidatorMock->method('validateJson')->willReturn(null);

        $jiraLoginMock = $this->createMock(JiraLogin::class);
        $jiraLoginMock->method('connect')->willReturn(new JsonResponse([
            'code' => 200,
            'message' => 'Jira account added successfully'
        ], Response::HTTP_OK));

        $data = [
            'url' => 'https://example.atlassian.net',
            'emailJira' => 'user@example.com',
            'token' => 'mockApiToken'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($data));

        $controller = new JiraLoginController();

        $response = $controller->index($jsonValidatorMock, $request, $jiraLoginMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 200, 'message' => 'Jira account added successfully']),
            $response->getContent()
        );
    }

    public function testDeleteJiraAccount()
    {
        $jiraInfoMock = $this->createMock(JiraInfo::class);
        $userMock = $this->createMock(\App\Entity\User::class);
        $userMock->method('getId')->willReturn(1);
        $jiraInfoMock->method('getUser')->willReturn($userMock);
        $jiraInfoRepositoryMock = $this->createMock(JiraInfoRepository::class);
        $jiraInfoRepositoryMock->expects($this->once())->method('deleteJiraInfo');

        $request = new Request();

        $controller = $this->getMockBuilder(JiraLoginController::class)
            ->onlyMethods(['getUser'])
            ->getMock();
        $controller->method('getUser')->willReturn($userMock); 

        $response = $controller->delete($jiraInfoMock, $jiraInfoRepositoryMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 200, 'message' => 'Jira account deleted']),
            $response->getContent()
        );
    }
}
