<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\BlogBundle\Entity\Post;

class BlogFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $post1 = new Post();
        $post1->setTitle('Lorem');
        $post1->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $post1->setImage('img1.jpg');
        $post1->setAuthor('admin');
        $manager->persist($post1);

        $post2 = new Post();
        $post2->setTitle('Lorem post');
        $post2->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus id, culpa itaque magni, nihil distinctio officiis, repellendus quis necessitatibus unde nisi, excepturi! Sit eligendi sunt in, quisquam consequuntur adipisci eveniet.');
        $post2->setImage('img2.jpg');
        $post2->setAuthor('user');
        $manager->persist($post2);

        $post3 = new Post();
        $post3->setTitle('Lorem blog');
        $post3->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores commodi ut placeat beatae, sapiente ratione quia quo. Facilis, alias, quia. Consequuntur blanditiis eligendi quibusdam tempore voluptatem, nemo amet, quidem culpa. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui nostrum, modi, aspernatur porro amet saepe. Reiciendis optio dolorum rerum enim voluptatum dolores saepe accusantium et. Voluptatibus a rem fuga ipsa?');
        $post3->setImage('img3.jpg');
        $post3->setAuthor('user 2');
        $manager->persist($post3);

        $manager->flush();
    }

}
