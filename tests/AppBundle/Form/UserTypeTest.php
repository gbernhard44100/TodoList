<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTypeTest extends TypeTestCase
{
    private $validator;

    protected function getExtensions()
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->validator
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));
        $this->validator
            ->method('getMetadataFor')
            ->will($this->returnValue(new ClassMetadata(Form::class)));

        return array(
            new ValidatorExtension($this->validator),
        );
    }
    
    public function testSubmitValidData()
    {
        $formData = array(
            'username' => 'testname',
            'password' => array('first' => 'motdepasse', 'second' => 'motdepasse'),
            'email' => 'testname@gmail.com',
            'roles' => 'ROLE_ADMIN'
        );

        $objectToCompare = new User();
        $form = $this->factory->create(UserType::class, $objectToCompare);
        
        $object = new User();
        $object->setUsername('testname');
        $object->setPassword('motdepasse');
        $object->setEmail('testname@gmail.com');
        $object->setRoles(array('ROLE_ADMIN'));
        
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        
        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}

