<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\ORM\LoadUser;
use AppBundle\Entity\Task;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class LoadTask implements FixtureInterface, DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $tasks = Yaml::parse(file_get_contents(__DIR__ . '/TaskData.yml'));
        $userRepository = $manager->getRepository('AppBundle:User');

        foreach ($tasks as $task) {
            $taskToPersist = new Task();
            $taskToPersist->setTitle($task['title']);
            $taskToPersist->setContent($task['content']);
            $taskToPersist->setCreatedAt(new \DateTime(date("Y-m-d H:i:s", $task['datetime'])));
            
            $user = $userRepository->findOneById($task['user_id']);
            $taskToPersist->setUser($user);
            
            $manager->persist($taskToPersist);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadUser::class,
        );
    }

}
