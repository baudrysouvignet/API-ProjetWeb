<?php

namespace App\Entity;

use App\Entity\Project\Lignes;
use App\Repository\JiraProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Lignes>
     */
    #[ORM\OneToMany(targetEntity: Lignes::class, mappedBy: 'project')]
    private Collection $lignes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Lignes>
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(Lignes $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setProject($this);
        }

        return $this;
    }

    public function removeLigne(Lignes $ligne): static
    {
        if ($this->lignes->removeElement($ligne)) {
            // set the owning side to null (unless already changed)
            if ($ligne->getProject() === $this) {
                $ligne->setProject(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
