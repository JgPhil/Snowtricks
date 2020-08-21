<?php

namespace App\Tests\Util;

trait EntityValidation
{

    public function validateOrGetError($entity)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($entity);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        return [$errors, $messages];
    }
}
