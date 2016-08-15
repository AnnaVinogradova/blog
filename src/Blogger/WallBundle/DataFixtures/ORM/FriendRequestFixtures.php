<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\WallBundle\Entity\FriendRequest;
use Blogger\BlogBundle\Entity\User;

class LoadFriendRequestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $request = new FriendRequest();
        $request->setSender($manager->merge($this->getReference('user')));
        $request->setReceiver($manager->merge($this->getReference('admin-user')));
        $request->setStatus(false);
        $manager->persist($request);

        $manager->flush();
    }

    public function getOrder()
    {
        return 7;
    }

}
