<?php

namespace App\Tests\Util;



trait AssertCheckResponse
{
    use EntityValidation;

    public function assertHasErrors($entity, int $number = 0)
    {
        [$errors, $messages] = $this->validateOrGetError($entity);
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

}