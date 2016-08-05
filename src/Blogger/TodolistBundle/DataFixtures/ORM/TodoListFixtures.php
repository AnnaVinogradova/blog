<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\TodolistBundle\Entity\TodoList;
use Blogger\BlogBundle\Entity\User;

class LoadTodoListData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $list1 = new TodoList();
        $list1->setName('Username list');
        $list1->setUser($manager->merge($this->getReference('user')));
        $manager->persist($list1);
        $this->addReference('list1', $list1);

        $list2 = new TodoList();
        $list2->setName('Admin list');    
        $list2->setUser($manager->merge($this->getReference('admin-user')));
        $manager->persist($list2);
        $this->addReference('list2', $list2);

        $list3 = new TodoList();
        $list3->setName('Common list from admin');
        $list3->setUser($manager->merge($this->getReference('admin-user')));
        $manager->persist($list3);
        $this->addReference('list3', $list3);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }

}
