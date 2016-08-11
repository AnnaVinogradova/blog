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

        // get access to post without authorization
        $crawler = $client->request('GET', '/post/');
        $crawler = $client->followRedirect();
        $this->assertContains('login', $client->getRequest()->getUri());

        $this->getLogin($client);

        $crawler = $client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('Log in');
        $form = $buttonCrawlerNode->form();
        $data = array('_username' => self::USERNAME,'_password' => self::PASSWORD);
        $client->submit($form,$data);

        // Create a new entry in the database
        $crawler = $client->request('GET', '/post/');        
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /post/");
        $crawler = $client->click($crawler->selectLink('Create a new post')->link());

        // Check create post without authorization
        $link = $this->checkLinkWithoutAuthorization($client);
        $crawler = $client->request('GET', $link);

        //create entity with empty title
        $form = $crawler->selectButton('Create')->form(array(
            'post[title]'  => '',
            'post[content]'  => 'test',
        ));

        $client->submit($form);
        $this->assertContains('This value should not be blank.', $client->getResponse()->getContent());

        //create entity with empty content
        $form = $crawler->selectButton('Create')->form(array(
            'post[title]'  => 'test',
            'post[content]'  => '',
        ));

        $client->submit($form);
        $this->assertContains('This value should not be blank.', $client->getResponse()->getContent());

        //create valid entity
        $form = $crawler->selectButton('Create')->form(array(
            'post[title]'  => 'Test Post in PostControllerTest',
            'post[content]'  => 'Test content',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();        
        $this->assertGreaterThan(0, $crawler->filter('h2:contains("Test Post in PostControllerTest")')->count(), 'Failed to create element');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        // Check edit post without authorization
        $link = $this->checkLinkWithoutAuthorization($client);
        $crawler = $client->request('GET', $link);

        //entity with empty title
        $form = $crawler->selectButton('Edit')->form(array(
            'post[title]'  => '',
            'post[content]'  => 'Test content',
        ));
        $client->submit($form);
        $this->assertContains('This value should not be blank.', $client->getResponse()->getContent());

        //entity with empty content
        $form = $crawler->selectButton('Edit')->form(array(
            'post[title]'  => 'test',
            'post[content]'  => '',
        ));
        $client->submit($form);
        $this->assertContains('This value should not be blank.', $client->getResponse()->getContent());

        //valid values
        $form = $crawler->selectButton('Edit')->form(array(
            'post[title]'  => 'Edited Test Post in PostControllerTest',
            'post[content]'  => 'Test content',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h2:contains("Edited Test Post in PostControllerTest")')->count(), 'Failed to update Post');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Edited Test Post in PostControllerTest/', $client->getResponse()->getContent());
    }

    private function getLogin($client)
    {
        $crawler = $client->request('GET', '/login');
        $buttonCrawlerNode = $crawler->selectButton('Log in');
        $form = $buttonCrawlerNode->form();
        $data = array('_username' => self::USERNAME,'_password' => self::PASSWORD);
        $client->submit($form,$data);
    }

    private function getLogout($client)
    {
        $client->request('GET', '/logout');
    }

    private function checkLinkWithoutAuthorization($client)
    {
        $link = $client->getRequest()->getUri();
        $this->getLogout($client);
        $crawler = $client->request('GET', $link);        
        $crawler = $client->followRedirect();
        $this->assertContains('login', $client->getRequest()->getUri());
        $this->getLogin($client);

        return $link;
    }

}
