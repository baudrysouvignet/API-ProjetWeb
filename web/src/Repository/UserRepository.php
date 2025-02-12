<?php

namespace App\Repository;

use App\Entity\JiraInfo;
use App\Entity\User;
use App\Service\Global\Cryptage;
use App\Service\Platforms\JiraInfoService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private JWTTokenManagerInterface $jwtManager;
    private JiraInfoService $jiraInfoService;
    private Cryptage $cryptage;

    public function __construct(ManagerRegistry $registry,
    JWTTokenManagerInterface $jwtManager, JiraInfoService $jiraInfoService, Cryptage $cryptage)
    {
        parent::__construct($registry, User::class);
        $this->jwtManager = $jwtManager;
        $this->jiraInfoService = $jiraInfoService;
        $this->cryptage = $cryptage;
    }

    public function setLastConnexion(User $user)
    {
        $user->setLastConnexion(new \DateTimeImmutable());
        $this->getEntityManager()->flush();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function getJWTToken(
        User $user
    )
    {
        return $this->jwtManager->create($user);
    }

    public function getJiraProject(User $user): array
    {
        $jiraInfos = $this->getEntityManager()->getRepository(JiraInfo::class)->findBy(['user' => $user]);
        $jiraProjects = [];
        foreach ($jiraInfos as $jiraInfo) {
            foreach ($jiraInfo->getJiraProjects() as $jiraProject) {
                $projectsIssues = $this->jiraInfoService->getJiraProjectsInfo(
                    $jiraInfo->getUrl(),
                    $this->cryptage->decrypt($jiraInfo->getApiToken()),
                    $jiraProject->getProjectJira()
                );

                $jiraProjects[] = [
                    'id' => $jiraProject->getId(),
                    'title' => $jiraProject->getTitle(),
                    'type' => 'jira',
                    'info' => [
                        'account' => $jiraProject->getJiraInfo()->getId(),
                        'issues' =>array_values(array_filter($projectsIssues, function($project) use ($jiraProject) {
                            return $project['id'] == $jiraProject->getIssueTypes();
                        }))[0]['name']
                    ]
                ];
            }
        }
        return $jiraProjects;
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
