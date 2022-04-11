<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton("Sign in")->form();
        $form['email']->setValue("TestUser@gmail.com");
        $form['password']->setValue("TestPassword");
        $crawler = $client->submit($form);

        $this->assertResponseRedirects("/user-panel");

        $crawler = $client->followRedirect();


        
        $this->assertEquals(1,$crawler->filter("a:contains('Logout')")->count());
        $this->assertEquals(1,$crawler->filter("a:contains('Your file panel')")->count());
        $this->assertEquals(1,$crawler->filter("label:contains('Upload file')")->count());
        $this->assertEquals(1,$crawler->filter("h4:contains('Welcome TestUser@gmail.com!')")->count());


        

    }
}
