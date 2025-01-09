<?php

namespace App\Repository;

use App\Entity\JiraInfo;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JiraInfo>
 */
class JiraInfoRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $_em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, JiraInfo::class);
        $this->_em = $em;
    }

    public function createJirainfo(
        User $user,
        string $url,
        string $token,
    )
    {
        $jiraInfo = (new JiraInfo())
            ->setUser($user)
            ->setUrl($url)
            ->setApiToken($token);
        $this->_em->persist($jiraInfo);
        $this->_em->flush();
    }
}
