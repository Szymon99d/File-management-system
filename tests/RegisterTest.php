<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;



class RegisterTest extends WebTestCase
{
    public function testRegister(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET','/');

        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Submit');
        $form = $button->form();
        $user = uniqid("User");
        $form['user[email]']->setValue("{$user}@gmail.com");
        $form['user[password][Password]']->setValue("TestPassword*&@^12");
        $form['user[password][RepeatPassword]']->setValue("TestPassword*&@^12");
        $form['user[username]']->setValue($user);
        
        $client->submit($form);

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email,"To","{$user}@gmail.com");
        $this->assertEmailTextBodyContains($email,"To confirm your email address click this link:");

       

        $this->assertResponseRedirects("/");
        $crawler = $client->followRedirect();

        $this->assertEquals(1,$crawler->filter("a:contains('Logout')")->count());
        $this->assertEquals(1,$crawler->filter("a:contains('Your file panel')")->count());
        $this->assertEquals(1,$crawler->filter(".alert")->count());
        
    
    }
}
