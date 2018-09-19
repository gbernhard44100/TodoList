<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class LoadUser implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $users = Yaml::parse(file_get_contents(__DIR__ . '/UserData.yml'));
        foreach ($users as $user) {
            $userToPersist = new User();
            $userToPersist->setUsername($user['username']);
            $userToPersist->setEmail($user['email']);
            $userToPersist->setPassword($user['password']);
            $userToPersist->setRoles(array($user['role']));
            $manager->persist($userToPersist);
        }
        $manager->flush();
    }

}
