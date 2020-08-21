<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\Util\AssertCheckResponse;
use App\Tests\Util\EntityValidation;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use EntityValidation;
    use AssertCheckResponse;

    public function getEntity(): User
    {
        return (new User())
            ->setUsername('Conan')
            ->setPassword('motdepasse')
            ->setcreatedAt(new \DateTime())
            ->setEmail('paul@gmail.com');
    }

    public function testValidUser()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidUser()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('123456'), 1);
        $this->assertHasErrors($this->getEntity()->setPassword('12345'), 1);
    }

    public function testInvalidBlankUsername()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 1);
    }

    public function testAlreadyUsedUsername()
    {
        $this->assertHasErrors($this->getEntity()->setUsername('Paul'), 1);
    }

}
