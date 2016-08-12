<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\WallBundle\Entity\WallPost;
use Blogger\BlogBundle\Entity\User;

class LoadWallPostData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $post1 = new WallPost();
        $post1->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl.');
        $post1->setImg('img1.jpg');
        $post1->setUser($manager->merge($this->getReference('admin-user')));        
        $post1->setWall($manager->merge($this->getReference('map1')));
        $manager->persist($post1);

        $post2 = new WallPost();
        $post2->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus id, culpa itaque magni, nihil distinctio officiis, repellendus quis necessitatibus unde nisi, excepturi! Sit eligendi sunt in, quisquam consequuntur adipisci eveniet. Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo.');
        $post2->setUser($manager->merge($this->getReference('user')));
        $post2->setWall($manager->merge($this->getReference('map1')));
        $manager->persist($post2);

        $post3 = new WallPost();
        $post3->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores commodi ut placeat beatae, sapiente ratione quia quo. Facilis, alias, quia. ');
        $post3->setImg('img3.jpg');
        $post3->setUser($manager->merge($this->getReference('user')));
        $post3->setWall($manager->merge($this->getReference('map2')));
        $manager->persist($post3);

        $manager->flush();
    }

    public function getOrder()
    {
        return 8;
    }

}
