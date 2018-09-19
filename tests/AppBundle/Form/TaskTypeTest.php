<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'title' => 'testname',
            'content' => 'Une chaine de plus de 50 charactères : Nunc vero inanes flatus'
                . ' quorundam vile esse quicquid extra urbis pomerium nascitur aestimant praeter'
                . ' orbos et caelibes, nec credi potest qua obsequiorum diversitate coluntur homines'
                . ' sine liberis Romae.',
        );

        $objectToCompare = new Task();
        $form = $this->factory->create(TaskType::class, $objectToCompare);
        
        $object = new Task();
        $object->setTitle('test');
        $object->setContent(
            'Une chaine de plus de 50 charactères : Nunc vero inanes flatus'
                . ' quorundam vile esse quicquid extra urbis pomerium nascitur aestimant praeter'
                . ' orbos et caelibes, nec credi potest qua obsequiorum diversitate coluntur homines'
                . ' sine liberis Romae.'
        );
        
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

