<?php

namespace App\Controller\Security;

use App\Service\Global\JsonValidator;
use App\Service\Security\ApiRegistration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ApiRegistrationController extends AbstractController
{
    #[Route('/api/registration', name: 'app_security_api_registration', methods: ['POST'])]
    public function index(
        Request $request,
        ApiRegistration $apiRegistration,
        JsonValidator $validator,
    ): JsonResponse
    {
        $jsonSchema = json_decode('{
            "type": "object",
            "properties": {
                "firstname": {"type": "string"},
                "name": {"type": "string"},
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
        $result = $apiRegistration->validateAndCreateUser($data);

        return $result;
    }
}
