<?php
namespace App\Tests\Controller\Platforms;

use App\Controller\Platforms\PlatformsController;
use App\Service\Platforms\PlatformsService;
use App\Service\Global\Cryptage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PlatformsControllerTest extends TestCase
{
    public function testIndex()
    {
        $platformsServiceMock = $this->createMock(PlatformsService::class);
        $platformsServiceMock->method('getPlatforms')->willReturn(['platform1', 'platform2']);
        
        $cryptageMock = $this->createMock(Cryptage::class);

        $controller = $this->getMockBuilder(PlatformsController::class)
            ->onlyMethods(['getUser'])
            ->getMock();

        $userMock = $this->createMock(\App\Entity\User::class);
        $controller->method('getUser')->willReturn($userMock);

        $response = $controller->index($platformsServiceMock, $cryptageMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'code' => 200,
                'data' => ['platform1', 'platform2']
            ]),
            $response->getContent()
        );
    }
}
