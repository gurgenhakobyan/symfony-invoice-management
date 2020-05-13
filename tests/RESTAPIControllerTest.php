<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RESTAPIControllerTest extends WebTestCase
{
    public function testListInvoices()
    {
        $client = static::createClient();

        $route = self::$container->get('router')->generate('api_list_invoices');

        $crawler = $client->request('GET', $route);

        $this->assertResponseIsSuccessful();
    }
}
