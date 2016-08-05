<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\BlogBundle\Entity\User;
use Blogger\TodolistBundle\Entity\TodoList;
use Blogger\TodolistBundle\Entity\Request;

class LoadRequestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $request1 = new Request();
        $request1->setTodolist($manager->merge($this->getReference('list3')));
        $request1->setUser($manager->merge($this->getReference('user')));
        $request1->setStatus(true);
        $manager->persist($request1);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }

}
