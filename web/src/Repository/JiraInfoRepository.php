<?php

namespace App\Repository;

use App\Entity\JiraInfo;
use App\Entity\User;
use App\Service\Global\Cryptage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JiraInfo>
 */
class JiraInfoRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $_em;
    private Cryptage $cryptage;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, Cryptage $cryptage)
    {
        parent::__construct($registry, JiraInfo::class);
        $this->_em = $em;
        $this->cryptage = $cryptage;
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

    public function deleteJiraInfo(JiraInfo $jiraInfo)
    {
        $this->_em->remove($jiraInfo);
        $this->_em->flush();
    }

    public function decrypteToken(JiraInfo $jiraInfo): string
    {
        return $this->cryptage->decrypt($jiraInfo->getApiToken());
    }
}
