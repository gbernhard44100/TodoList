<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $userOne;
    private $admin;

    public function setUp()
    {
        $this->userOne = $this->getConnectedClient('user_1', 'user_1');
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
        $this->admin->request('GET', $url);
        $this->assertTrue($this->admin->getResponse()->isSuccessful());
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
    
    /**
     * @dataProvider provideUrls
     */
    public function testPageRefusedIfUser($url)
    {
        $this->userOne->request('GET', $url);
        $this->assertSame(403, $this->userOne->getResponse()->getStatusCode());
    }
    
    /**
     * @dataProvider provideUsers
     */
    public function testCreateUsers($user)
    {
        $crawler = $this->admin->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[roles]'][$user['roleNb']]->tick();
        $this->admin->submit($form, array(
            'user[username]' => $user['username'],
            'user[password][first]' => $user['password'],
            'user[password][second]' => $user['password'],
            'user[email]' => $user['email'],
        ));
        $crawler = $this->admin->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $username = $crawler->filter('table.table')->filter('tr')->last()->filter('td')->eq(0)->html();
        $status = $crawler->filter('table.table')->filter('tr')->last()->filter('td')->eq(2)->html();
        $this->assertSame($user['username'], $username);
        $this->assertSame($user['role'], $status);
    }
    
    public function testEditUser()
    {
        $crawler = $this->admin->request('GET', '/users');
        $link = $crawler->filter('table.table')->filter('tr')->last()->selectLink('Edit')->link();/*->selectButton('Edit');*/
        $crawler = $this->admin->click($link);
        $form = $crawler->selectButton('Modifier')->form();
        $form['user[roles]'][0]->tick();
        $form['user[roles]'][1]->untick();
        $this->admin->submit($form, array(
            'user[username]' => 'newName',
            'user[password][first]' => 'newPassword',
            'user[password][second]' => 'newPassword',
            'user[email]' => 'new.name@gmail.com',
        ));
        $crawler = $this->admin->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $username = $crawler->filter('table.table')->filter('tr')->last()->filter('td')->eq(0)->html();
        $status = $crawler->filter('table.table')->filter('tr')->last()->filter('td')->eq(2)->html();
        $this->assertSame('newName', $username);
        $this->assertSame('utilisateur', $status);
    }
    
    public function provideUrls()
    {
        return array(
            array(
                '/users',
                '/users/create',
                '/users/1/edit'
            )
        );
    }
    
    public function provideUsers()
    {
        return array(
            array(
                array(
                    'username' => 'GaetanUser',
                    'password' => 'motdepassse1',
                    'email' => 'gaetanuser@gmail.com',
                    'roleNb' => 0,
                    'role' => 'utilisateur'
                )
            ),
            array(
                array(
                    'username' => 'GaetanAdmin',
                    'password' => 'motdepassse2',
                    'email' => 'gaetanadmin@gmail.com',
                    'roleNb' => 1,
                    'role' => 'administrateur'
                )
            )
        );
    }
    
}
