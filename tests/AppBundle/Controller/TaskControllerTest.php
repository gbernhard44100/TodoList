<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private $userOne;
    private $userTwo;
    private $admin;

    public function setUp()
    {
        $this->userOne = $this->getConnectedClient('user_1', 'user_1');
        $this->userTwo = $this->getConnectedClient('user_2', 'user_2');
        $this->admin = $this->getConnectedClient('admin', 'admin');
    }
    
    public function getConnectedClient(string $username, string $password)
    {
        $client = self::createClient();
        $crawler = $client->request('GET', 'login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues(array(
            '_username' => $username,
            '_password'   => $password,
        ));
        $client->submit($form);
        return $client;
    }
            
    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $this->userOne->request('GET', $url);
        $this->assertTrue($this->userOne->getResponse()->isSuccessful());
    }
    
    /**
     * @dataProvider provideUrls
     */
    public function testPageIfNotLogin($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $crawler = $client->followRedirect();
        
        $this->assertSame('http://localhost/login', $crawler->getUri());
    }
    
    public function testCreateTask()
    {
        $crawler = $this->userTwo->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $this->userTwo->submit($form, array(
            'task[title]' => 'Finir le projet 8',
            'task[content]' => 'Il faut le terminer impérativement pour Septembre.'
        ));
        $crawler = $this->userTwo->followRedirect();
        
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        
        $crawler = $crawler->filter('div.thumbnail')->last();
        $this->assertCount(1, $crawler->filter('h5:contains("user_2")'));
    }
    
    public function testEditTask()
    {
        $crawler = $this->userOne->request('GET', '/tasks');
        $link = $crawler->filter('h5:contains("user_2")')->parents()->filter('h4 > a')->link();
        $crawler = $this->userOne->click($link);
        $form = $crawler->selectButton('Modifier')->form();
        $this->userOne->submit($form, array(
            'task[title]' => 'Modification : Finir le projet 8',
            'task[content]' => 'J\'ai modifié ta tâche. Merci de prendre en compte mes nouvelles instructions.'
        ));
        $crawler = $this->userOne->followRedirect();
        
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
        
        $crawler = $crawler->filter('h4:contains("Modification : Finir le projet 8")')->parents();
        $this->assertSame('Tâche créée par : user_2', $crawler->filter('h5')->html());
    }
    
    public function testDeleteTaskAsUser()
    {
        $crawler = $this->userOne->request('GET', '/tasks');
        $start = $crawler->filter('button:contains("Supprimer")')->count();
        $crawler = $crawler->filter('h5:contains("user_1")')->parents()->parents();
        $form = $crawler->selectButton('Supprimer')->form();
        $this->userOne->submit($form);
        $crawler = $this->userOne->followRedirect();
        
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
        
        $this->assertCount(($start - 1), $crawler->filter('button:contains("Supprimer")'));
    }
    
    public function testDeleteAnonymTaskAsUser()
    {
        $crawler = $this->userOne->request('GET', '/tasks');
        $start = $crawler->filter('button:contains("Supprimer")')->count();
        $crawler = $crawler->filter('h5:contains("Auteur anonyme")')->parents()->parents();
        $form = $crawler->selectButton('Supprimer')->form();
        $this->userOne->submit($form);
        $crawler = $this->userOne->followRedirect();
        
        $this->assertCount(1, $crawler->filter('div.alert.alert-danger'));
        $this->assertCount(($start), $crawler->filter('button:contains("Supprimer")'));
    }
    
    public function testUserDeleteSomeoneTask()
    {
        $crawler = $this->userOne->request('GET', '/tasks');
        $start = $crawler->filter('button:contains("Supprimer")')->count();
        $crawler = $crawler->filter('h5:contains("user_2")')->parents()->parents();
        $form = $crawler->selectButton('Supprimer')->form();
        $this->userOne->submit($form);
        $crawler = $this->userOne->followRedirect();
        
        $this->assertCount(1, $crawler->filter('div.alert.alert-danger'));
        $this->assertCount(($start), $crawler->filter('button:contains("Supprimer")'));
    }
    
    public function testDeleteTaskAsAdmin()
    {
        $crawler = $this->admin->request('GET', '/tasks');
        $start = $crawler->filter('button:contains("Supprimer")')->count();
        $crawler = $crawler->filter('h5:contains("Auteur anonyme")')->parents()->parents();
        $form = $crawler->selectButton('Supprimer')->form();
        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
        $this->assertCount(($start - 1), $crawler->filter('button:contains("Supprimer")'));
    }
    
    public function testToggleTask()
    {
        $crawler = $this->userOne->request('GET', '/tasks');
        $start = $crawler->filter('button:contains("Marquer comme faite")')->count();
        $form = $crawler->selectButton('Marquer comme faite')->form();
        $this->userOne->submit($form);
        $crawler = $this->userOne->followRedirect();
        
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
        $this->assertCount(($start - 1), $crawler->filter('button:contains("Marquer comme faite")'));
    }
    
    public function provideUrls()
    {
        return array(
            array(
                '/tasks',
                '/tasks/create',
                '/tasks/1/edit'
            )
        );
    }
}
