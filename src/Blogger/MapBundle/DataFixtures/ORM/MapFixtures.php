<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\BlogBundle\Entity\User;
use Blogger\MapBundle\Entity\Map;

class LoadMapData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $map = new Map();
        $map->setUser($manager->merge($this->getReference('user')));
        $manager->persist($map);
        $this->addReference('map1', $map);

        $map2 = new Map();
        $map2->setUser($manager->merge($this->getReference('admin-user')));
        $manager->persist($map2);
        $this->addReference('map2', $map2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }

}
