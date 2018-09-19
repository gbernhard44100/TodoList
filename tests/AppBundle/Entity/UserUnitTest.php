<?php

namespace Test\AppBundle\Entity;

use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    private $user;
    
    public function setUp()
    {
        $this->user = new User();
    }
    
    public function testGettersAndSetters()
    {
        $this->user->setUsername('Gaetan');
        $this->user->setPassword('motdepasse');
        $this->user->setEmail('gaetan.bernhard@gmail.com');
        $this->user->setRoles(array('ROLE_ADMIN'));
        
        $this->assertSame('Gaetan', $this->user->getUsername());
        $this->assertSame('motdepasse', $this->user->getPassword());
        $this->assertSame('gaetan.bernhard@gmail.com', $this->user->getEmail());
        $this->assertSame(null, $this->user->getSalt());
        $this->assertSame(array('ROLE_ADMIN'), $this->user->getRoles());
    }
}
