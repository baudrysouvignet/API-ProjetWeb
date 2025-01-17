<?php

namespace App\Tests\Service\Global;

use App\Service\Global\JsonValidator;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

class JsonValidatorTest extends TestCase
{
    private JsonValidator $jsonValidator;

    protected function setUp(): void
    {
        $this->jsonValidator = new JsonValidator();
    }

    public function testValidateJsonWithValidJson(): void
    {
        $jsonData = (object)[
            'name' => 'John Doe',
            'age' => 30
        ];

        $jsonSchema = (object)[
            'type' => 'object',
            'properties' => (object)[
                'name' => (object)['type' => 'string'],
                'age' => (object)['type' => 'integer']
            ],
            'required' => ['name', 'age']
        ];

        $result = $this->jsonValidator->validateJson($jsonData, $jsonSchema);

        $this->assertNull($result);
    }

    public function testValidateJsonWithInvalidJson(): void
    {
        $jsonData = (object)[
            'name' => 'John Doe'
        ];

        $jsonSchema = (object)[
            'type' => 'object',
            'properties' => (object)[
                'name' => (object)['type' => 'string'],
                'age' => (object)['type' => 'integer']
            ],
            'required' => ['name', 'age']
        ];

        $result = $this->jsonValidator->validateJson($jsonData, $jsonSchema);

        $this->assertIsString($result);
        $this->assertStringContainsString('age', $result);
    }

    public function testValidateJsonWithTypeMismatch(): void
    {
        $jsonData = (object)[
            'name' => 'John Doe',
            'age' => 'thirty'
        ];

        $jsonSchema = (object)[
            'type' => 'object',
            'properties' => (object)[
                'name' => (object)['type' => 'string'],
                'age' => (object)['type' => 'integer']
            ],
            'required' => ['name', 'age']
        ];

        $result = $this->jsonValidator->validateJson($jsonData, $jsonSchema);

        $this->assertIsString($result);
        $this->assertStringContainsString('age', $result);
        $this->assertStringContainsString('integer', $result);
    }
}
