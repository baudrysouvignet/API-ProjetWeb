<?php

namespace App\Entity;

use App\Repository\JiraProjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JiraProjectRepository::class)]
class JiraProject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Title = null;

    #[ORM\ManyToOne(inversedBy: 'jiraProjects')]
    private ?JiraInfo $JiraInfo = null;

    #[ORM\Column]
    private ?int $ProjectJira = null;

    #[ORM\Column]
    private ?int $IssueTypes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): static
    {
        $this->Title = $Title;

        return $this;
    }

    public function getJiraInfo(): ?JiraInfo
    {
        return $this->JiraInfo;
    }

    public function setJiraInfo(?JiraInfo $JiraInfo): static
    {
        $this->JiraInfo = $JiraInfo;

        return $this;
    }

    public function getProjectJira(): ?int
    {
        return $this->ProjectJira;
    }

    public function setProjectJira(int $ProjectJira): static
    {
        $this->ProjectJira = $ProjectJira;

        return $this;
    }

    public function getIssueTypes(): ?int
    {
        return $this->IssueTypes;
    }

    public function setIssueTypes(int $IssueTypes): static
    {
        $this->IssueTypes = $IssueTypes;

        return $this;
    }
}
