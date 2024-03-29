<?php


namespace App\Tests\Functionnal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicTest extends WebTestCase
{
    public function testEnvironnementIsOk(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/');
        $this->assertResponseIsSuccessful();
    }
}