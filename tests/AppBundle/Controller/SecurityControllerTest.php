<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $connectedClient;
    
    public function setUp()
    {
        $this->connectedClient = self::createClient();
        $crawler = $this->connectedClient->request('GET', 'login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues(array(
            '_username' => 'user_1',
            '_password'   => 'user_1',
        ));
        $this->connectedClient->submit($form);
    }       
    
    /**
     * @dataProvider provideUrls
     */
    public function testLogout($url)
    {
        $crawler = $this->connectedClient->request('GET', '/logout');
        $this->connectedClient->followRedirect();
        $this->connectedClient->request('GET', $url);
        $crawler = $this->connectedClient->followRedirect();
        
        $this->assertSame('http://localhost/login', $crawler->getUri());
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
