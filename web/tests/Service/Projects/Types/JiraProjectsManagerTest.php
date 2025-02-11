<?php

namespace App\Tests\Service\Projects\Types;

use App\Service\Projects\Types\JiraProjectsManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class JiraProjectsManagerTest extends TestCase
{
    public function testIsValidate(): void
    {
        $jiraManager = new JiraProjectsManager();

        $this->assertTrue($jiraManager->isValidate('jira'));
        $this->assertFalse($jiraManager->isValidate('other'));
    }

    public function testCreateProject(): void
    {
        $jiraManager = new JiraProjectsManager();

        $data = ['type' => 'jira', 'name' => 'Test Project'];

        
        $response = $jiraManager->createProject($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 200, 'message' => 'Jira project created']),
            $response->getContent()
        );
    }
}
