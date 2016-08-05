<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\TodolistBundle\Entity\TodoList;
use Blogger\TodolistBundle\Entity\Task;

class LoadTaskData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $task1 = new Task();
        $task1->setName('task1 for username list');
        $task1->setStatus(true);
        $task1->setTodolist($manager->merge($this->getReference('list1')));
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setName('task2 for username list');
        $task2->setStatus(false);
        $task2->setTodolist($manager->merge($this->getReference('list1')));
        $manager->persist($task2);

        $task3 = new Task();
        $task3->setName('task3 for username list');
        $task3->setStatus(true);
        $task3->setTodolist($manager->merge($this->getReference('list1')));
        $manager->persist($task3);

        $task4 = new Task();
        $task4->setName('task1 for admin list');
        $task4->setStatus(true);
        $task4->setTodolist($manager->merge($this->getReference('list2')));
        $manager->persist($task4);

        $task5 = new Task();
        $task5->setName('task2 for admin list');
        $task5->setStatus(false);
        $task5->setTodolist($manager->merge($this->getReference('list2')));
        $manager->persist($task5);

        $task6 = new Task();
        $task6->setName('task1 for common list');
        $task6->setStatus(true);
        $task6->setTodolist($manager->merge($this->getReference('list3')));
        $manager->persist($task6);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }

}
