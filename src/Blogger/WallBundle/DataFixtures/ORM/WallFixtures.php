<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\WallBundle\Entity\Wall;
use Blogger\BlogBundle\Entity\User;

class LoadWallData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $wall1 = new Wall();
        $wall1->setUser($manager->merge($this->getReference('admin-user')));
        $manager->persist($wall1);
        $this->addReference('wall1', $wall1);

        $wall2 = new Wall();
        $wall2->setUser($manager->merge($this->getReference('user')));
        $manager->persist($wall2);        
        $this->addReference('wall2', $wall2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 7;
    }

}
