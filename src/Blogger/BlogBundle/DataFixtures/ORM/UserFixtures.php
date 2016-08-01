<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Blogger\BlogBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('username');
        $user->setEmail('email@domain.com');
        $user->setPlainPassword('password');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_USER'));
        $userManager->updateUser($user, true);
        $this->addReference('user', $user);

        $adminUser = $userManager->createUser();
        $adminUser->setUsername('admin');
        $adminUser->setEmail('admin@domain.com');
        $adminUser->setPlainPassword('123456');
        $adminUser->setEnabled(true);
        $adminUser->setRoles(array('ROLE_ADMIN'));
        $userManager->updateUser($adminUser, true);

        $this->addReference('admin-user', $adminUser);
    }

    public function getOrder()
    {
        return 1;
    }
    
}
