<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Service\Global\JsonValidator;
use App\Service\Security\ApiLogin;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_security_api_login', methods: ['POST'])]
    public function index(
        JsonValidator $validator,
        EntityManagerInterface $entityManager,
        Request $request,
        JWTTokenManagerInterface $JWTTokenManager,
        ApiLogin $apiLogin
    ): JsonResponse
    {
        $jsonSchema = json_decode('{
            "type": "object",
            "properties": {
                "password": {"type": "string"},
                "email": {"type": "string", "format": "email"}
            },
            "required": ["password", "email"]
        }');
        $validate = $validator->validateJson(json_decode($request->getContent(), false), $jsonSchema);

        if ($validate) {
            return new JsonResponse([
                'code' => 400,
                'message' => $validate
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        return $apiLogin->createToken($data);

    }

    #[Route('/api/user/info', name: 'app_security_get_user', methods: ['GET'])]
    public function user(): JsonResponse
    {
        return new JsonResponse([
            'email' => $this->getUser()->getEmail(),
            'firstName' => $this->getUser()->getFirstname(),
            'nom' => $this->getUser()->getName()
        ]);
    }
}
