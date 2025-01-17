<?php

namespace App\Entity;

use App\Repository\JiraInfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JiraInfoRepository::class)]
class JiraInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'jiraAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 1080)]
    private ?string $apiToken = null;

    /**
     * @var Collection<int, JiraProject>
     */
    #[ORM\OneToMany(targetEntity: JiraProject::class, mappedBy: 'JiraInfo')]
    private Collection $jiraProjects;

    public function __construct()
    {
        $this->jiraProjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return Collection<int, JiraProject>
     */
    public function getJiraProjects(): Collection
    {
        return $this->jiraProjects;
    }

    public function addJiraProject(JiraProject $jiraProject): static
    {
        if (!$this->jiraProjects->contains($jiraProject)) {
            $this->jiraProjects->add($jiraProject);
            $jiraProject->setJiraInfo($this);
        }

        return $this;
    }

    public function removeJiraProject(JiraProject $jiraProject): static
    {
        if ($this->jiraProjects->removeElement($jiraProject)) {
            // set the owning side to null (unless already changed)
            if ($jiraProject->getJiraInfo() === $this) {
                $jiraProject->setJiraInfo(null);
            }
        }

        return $this;
    }
}
