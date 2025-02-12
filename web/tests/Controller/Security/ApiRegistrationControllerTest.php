<?php 

namespace App\Tests\Controller\Security;

use App\Controller\Security\ApiRegistrationController;
use App\Service\Global\JsonValidator;
use App\Service\Security\ApiRegistration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiRegistrationControllerTest extends TestCase
{
    public function testSuccessfulRegistration()
    {
        $jsonValidatorMock = $this->createMock(JsonValidator::class);
        $jsonValidatorMock->method('validateJson')->willReturn(null);

        $apiRegistrationMock = $this->createMock(ApiRegistration::class);
        $apiRegistrationMock->method('validateAndCreateUser')->willReturn(new JsonResponse([
            'code' => 201,
            'message' => 'User registered successfully'
        ], JsonResponse::HTTP_CREATED));

        $controller = new ApiRegistrationController();
        $request = new Request([], [], [], [], [], [], json_encode([
            'firstname' => 'John',
            'name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecurePassword123'
        ]));

        $response = $controller->index($request, $apiRegistrationMock, $jsonValidatorMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 201, 'message' => 'User registered successfully']),
            $response->getContent()
        );
    }

    public function testRegistrationWithInvalidJson()
    {
        $jsonValidatorMock = $this->createMock(JsonValidator::class);
        $jsonValidatorMock->method('validateJson')->willReturn('Invalid JSON format');
        $apiRegistrationMock = $this->createMock(ApiRegistration::class);
        $controller = new ApiRegistrationController();

        $request = new Request([], [], [], [], [], [], json_encode([
            'firstname' => 'John',
            'name' => 'Doe',
            'password' => 'SecurePassword123'
        ]));

        $response = $controller->index($request, $apiRegistrationMock, $jsonValidatorMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 400, 'message' => 'Invalid JSON format']),
            $response->getContent()
        );
    }
}
