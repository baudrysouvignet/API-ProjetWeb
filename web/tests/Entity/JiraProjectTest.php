<?php

namespace App\Tests\Entity;

use App\Entity\JiraInfo;
use App\Entity\JiraProject;
use PHPUnit\Framework\TestCase;

class JiraProjectTest extends TestCase
{
    public function testSetAndGetTitle()
    {
        $project = new JiraProject();
        $title = 'Test';
        $project->setTitle($title);
        $this->assertEquals($title, $project->getTitle());
    }

    public function testSetAndGetProjectJira()
    {
        $project = new JiraProject();
        $projectJira = 123;
        $project->setProjectJira($projectJira);
        $this->assertEquals($projectJira, $project->getProjectJira());
    }

    public function testSetAndGetIssueTypes()
    {
        $project = new JiraProject();
        $issueTypes = 4;
        $project->setIssueTypes($issueTypes);
        $this->assertEquals($issueTypes, $project->getIssueTypes());
    }

    public function testSetAndGetJiraInfo()
    {
        $project = new JiraProject();
        $jiraInfoMock = $this->createMock(JiraInfo::class);
        $project->setJiraInfo($jiraInfoMock);
        $this->assertSame($jiraInfoMock, $project->getJiraInfo());
    }
}
