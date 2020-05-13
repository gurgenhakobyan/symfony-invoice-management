<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();

        $route = self::$container->get('router')->generate('main');

        $client->request('GET', $route);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome to Invoices Page');
        $this->assertSelectorTextContains('h3', 'Upload a new file');
    }

    public function testUploadAction()
    {
        $client = static::createClient();

        $route = self::$container->get('router')->generate('main');
        $crawler = $client->request('GET', $route);

        $buttonCrawlerNode = $crawler->selectButton('Submit');
        $form = $buttonCrawlerNode->form();

        $form['invoice[fileName]']->upload(__DIR__ . '/test-file.csv');

        $client->submit($form);

        $this->assertResponseIsSuccessful();

        $crawler = $client->request('GET', $route);

        //assert valid row
        $this->assertEquals('2',
            $crawler->filter('table#table-invoices th[scope="row"]')->first()->html()
        );

        //assert invalid row
        $this->assertEquals('3',
            $crawler->filter('table#table-errors td')->first()->html()
        );
    }
}
