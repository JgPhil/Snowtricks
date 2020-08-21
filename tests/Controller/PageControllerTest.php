<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{


    public function testIndexPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $response = $client->getResponse()->getStatusCode();
        $this->assertEquals(200, $response);
    }

    public function testFigureDetailPage()
    {
        $client = static::createClient();
        $figures = self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getRepository(Figure::class)
            ->findAll();
        $figuresArray = [];
        foreach ($figures as $figure) {
            $figuresArray[] = $figure->getId();
        }
        $randomFigureId = $figuresArray[array_rand($figuresArray, 1)];
        $crawler = $client->request('GET', '/figure/' . $randomFigureId);
        var_dump($crawler->getUri());
        $response = $client->getResponse()->getStatusCode();
        $this->assertEquals(200, $response);
    }
}
