<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\BlogBundle\Entity\User;
use Blogger\MapBundle\Entity\Map;
use Blogger\MapBundle\Entity\MapResolver;

class LoadMapResolverData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $resolver = new MapResolver();
        $resolver->setMap($manager->merge($this->getReference('map2')));
        $resolver->setUser($manager->merge($this->getReference('user')));
        $resolver->setStatus(false);
        $manager->persist($resolver);

        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }

}
