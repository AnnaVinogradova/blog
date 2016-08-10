<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\BlogBundle\Entity\User;
use Blogger\MapBundle\Entity\Map;
use Blogger\MapBundle\Entity\Marker;

class LoadMarkerData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $marker = new Marker();
        $marker->setTitle('Minsk');
        $marker->setLat(53.888153941285);
        $marker->setLng(27.57568359375);
        $marker->setMap($manager->merge($this->getReference('map2')));
        $manager->persist($marker);

        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }

}
