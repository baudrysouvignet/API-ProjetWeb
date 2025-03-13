<?php

namespace App\Entity\Project;

use App\Entity\JiraInfo;
use App\Entity\JiraProject;
use App\Repository\Project\LignesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LignesRepository::class)]
class Lignes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    private ?TypesLignes $Type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prompt = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JiraProject $project = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?TypesLignes
    {
        return $this->Type;
    }

    public function setType(?TypesLignes $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    public function getPrompt(): ?string
    {
        return $this->prompt;
    }

    public function setPrompt(string $prompt): static
    {
        $this->prompt = $prompt;

        return $this;
    }

    public function getProject(): ?JiraProject
    {
        return $this->project;
    }

    public function setProject(?JiraProject $project): static
    {
        $this->project = $project;

        return $this;
    }
}
