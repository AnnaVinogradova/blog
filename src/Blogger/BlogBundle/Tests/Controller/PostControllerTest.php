<?php

namespace Blogger\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    
    const USERNAME = 'username';
    const PASSWORD = 'password';

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('Log in');
        $form = $buttonCrawlerNode->form();
        $data = array('_username' => self::USERNAME,'_password' => self::PASSWORD);
        $client->submit($form,$data);

        // Create a new entry in the database
        $crawler = $client->request('GET', '/post/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /post/");
        $crawler = $client->click($crawler->selectLink('Create a new post')->link());
        $form = $crawler->selectButton('Create')->form(array(
            'post[title]'  => 'Test Post in PostControllerTest',
            'post[content]'  => 'Test content',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('h2:contains("Test Post in PostControllerTest")')->count(), 'Failed to create element');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Edit')->form(array(
            'post[title]'  => 'Edited Test Post in PostControllerTest',
            'post[content]'  => 'Test content',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('h2:contains("Edited Test Post in PostControllerTest")')->count(), 'Failed to update Post');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Edited Test Post in PostControllerTest/', $client->getResponse()->getContent());
    }

}
