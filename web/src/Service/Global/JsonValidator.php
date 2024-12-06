<?php

namespace App\Service\Global;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;

class JsonValidator
{
    public function validateJson($jsonData, $jsonSchema): ?string
    {
        $validator = new Validator();
        $validator->validate($jsonData, $jsonSchema, Constraint::CHECK_MODE_APPLY_DEFAULTS);

        if ($validator->isValid()) {
            return Null;
        }
        $text = "Le JSON n'est pas valide : ";
        foreach ($validator->getErrors() as $error) {
            $text .= sprintf("[%s] %s\n", $error['property'], $error['message']);
        }
        return $text;
    }
}