<?php

namespace App\Repository;

use App\Entity\JiraInfo;
use App\Entity\JiraProject;
use App\Entity\Project\Lignes;
use App\Entity\Project\TypesLignes;
use App\Service\Global\Cryptage;
use App\Service\Global\RequestApi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<JiraProject>
 */
class JiraProjectRepository extends ServiceEntityRepository
{
    private $cryptage;
    private $requestApi;
    private $em;
    public function __construct(ManagerRegistry $registry, Cryptage $cryptage, RequestApi $requestApi, EntityManagerInterface $em)
    {
        parent::__construct($registry, JiraProject::class);
        $this->cryptage = $cryptage;
        $this->requestApi = $requestApi;
        $this->em = $em;
    }

    public function createProject(
        JiraInfo $account,
        int $id,
        int $issues,
    ): JsonResponse
    {
        if (!$this->testProjectInfo(
            $this->cryptage->decrypt($account->getApiToken()),
            $account->getUrl(),
            $id,
            $issues
        )) {
            return new JsonResponse([
                'code' => 400,
                'message' => 'Invalid project info'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingProject = $this->findOneBy([
            'JiraInfo' => $account,
            'ProjectJira' => $id,
            'IssueTypes' => $issues
        ]);

        if ($existingProject) {
            return new JsonResponse([
                'code' => 400,
                'message' => 'Project already exists'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $project = (new JiraProject())
            ->setIssueTypes($issues)
            ->setJiraInfo($account)
            ->setProjectJira($id)
            ->setTitle('Project Jira');
        $this->em->persist($project);
        $this->addDefaultLignes($project);
        $this->em->flush();



        return new JsonResponse([
            'code' => 200,
            'message' => 'Project created'
        ], JsonResponse::HTTP_OK);
    }

    private function addDefaultLignes(
        JiraProject $project
    )
    {
        for ($i = 0; $i < 2; $i++) {
            $lignes = (new Lignes())
                ->setProject($project)
                ->setType(
                    $this->em->getRepository(TypesLignes::class)->findAll()[$i]
                );
            $this->em->persist($lignes);
        }
    }

    private function testProjectInfo(
        string $token,
        string $url,
        int $idProject,
        int $idIssue
    )
    {
        $apiUrl = "https://$url/rest/api/3/project/$idProject";

        $headers = [
            'Authorization' => "Basic $token",
            'Content-Type' => 'application/json',
        ];

        try {
            $value = $this->requestApi->send('GET', $apiUrl, $headers);
            $exist = false;

            foreach ($value['issueTypes'] as $issue) {
                if ($issue['id'] == $idIssue) {
                    $exist = true;
                    break;
                }
            }
            return $exist;
        } catch (\Exception $e) {
            return false;
        }

    }

    public function findDataForTicket(JiraProject $project): array
    {
        $token = $this->cryptage->decrypt($project->getJiraInfo()->getApiToken());
        $url = $project->getJiraInfo()->getUrl();
        $projectId = $project->getProjectJira();

        $apiUrl = "https://$url/rest/api/3/project/$projectId";

        $headers = [
            'Authorization' => "Basic $token",
            'Content-Type' => 'application/json',
        ];
        $value = $this->requestApi->send('GET', $apiUrl, $headers);

        $return = ['key' => $value['key']];
        foreach ($value['issueTypes'] as $issue) {
            if ($issue['id'] == $project->getIssueTypes()) {
                $return['issue'] = $issue['name'];
                break;
            }
        }
        if (!isset($return['issue'])) {
            return [];
        }
        return $return;
    }

    public function createTicket(
        string $url,
        string $token,
        array $data,
        array $params,
    )
    {
        $apiUrl = "https://$url/rest/api/3/issue";

        $headers = [
            'Authorization' => "Basic $token",
            'Content-Type' => 'application/json',
        ];

        $data = [
            "fields" => [
                "project" => [
                    "key" => $params['key']
                ],
                "summary" => $data['title'],
                "description" => [
                    "version" => 1,
                    "type" => "doc",
                    "content" => [
                        [
                            "type" => "paragraph",
                            "content" => [
                                [
                                    "type" => "text",
                                    "text" => $data['description']
                                ]
                            ]
                        ]
                    ]
                ],
                "issuetype" => [
                    "name" => $params['issue']
                ],
                "priority"=> [
                    "name"=> $data['priority']
                ]
            ]
        ];

        $this->requestApi->send('POST', $apiUrl, $headers, $data);
    }

    //    /**
    //     * @return JiraProject[] Returns an array of JiraProject objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('j.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?JiraProject
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
