<?php

namespace Blogger\TodolistBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoListControllerTest extends WebTestCase
{
    
    const USERNAME = 'username';
    const PASSWORD = 'password';

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // get access to list without authorization
        $crawler = $client->request('GET', '/todolist/');
        $crawler = $client->followRedirect();
        $this->assertContains('login', $client->getRequest()->getUri());

        $this->getLogin($client);

        // Create a new entry in the database
        $crawler = $client->request('GET', '/todolist/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /todolist/");
        $link = $crawler->selectLink('Create a new list')->link();
        $crawler = $client->click($link);

        // Check create page without authorization
        $link = $this->checkLinkWithoutAuthorization($client);

        $crawler = $client->request('GET', $link);

        // create entity with empty name
        $form = $crawler->selectButton('Create')->form(array(
            'todo_list[name]'  => '',
        ));
        $client->submit($form);
        $this->assertContains('This value should not be blank.', $client->getResponse()->getContent());

        //create valid entity
        $form = $crawler->selectButton('Create')->form(array(
            'todo_list[name]'  => 'Test for TodoListController',
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Test for TodoListController")')->count(), 'Fialed to find created element');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        // Check edit page without authorization
        $link = $this->checkLinkWithoutAuthorization($client);

        $crawler = $client->request('GET', $link);

        //empty entity for edit
        $form = $crawler->selectButton('Edit')->form(array(
            'todo_list[name]'  => '',
        ));
        $client->submit($form);
        $this->assertContains('This value should not be blank.', $client->getResponse()->getContent());

        //valid entity
        $form = $crawler->selectButton('Edit')->form(array(
            'todo_list[name]'  => 'Edited Test for TodoListController',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('[value="Edited Test for TodoListController"]')->count(), 'Failed to find edited element');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();
        $this->assertNotRegExp('/Edited Test/', $client->getResponse()->getContent());
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
