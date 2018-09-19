<?php

namespace Test\AppBundle\Entity;

use AppBundle\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskUnitTest extends TestCase
{
    private $task;
    
    public function setUp()
    {
        $this->task = new Task();
    }
    
    public function testGettersAndSetters()
    {
        $this->task->setTitle('Ma nouvelle tâche');
        $this->task->setContent('Il faudra réaliser tous les livrables demandés pour accomplir la tâche.');
        $this->task->setIsDone(false);
        
        $date = new \DateTime();
        $this->task->setCreatedAt($date);

        $this->assertSame('Ma nouvelle tâche', $this->task->getTitle());
        $this->assertSame(
            'Il faudra réaliser tous les livrables demandés pour accomplir la tâche.',
            $this->task->getContent()
        );
        $this->assertSame($date, $this->task->getCreatedAt());
        $this->assertSame(false, $this->task->isDone());
        $this->assertSame(false, $this->task->getIsDone());
    }
}
