<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    private function testPage($method, $url)
    {
        $client = static::createClient();
        $crawler = $client->request($method, $url);
        $response = $client->getResponse()->getStatusCode();
        return $this->assertEquals(200, $response);
    }

    public function testIndexPage()
    {
        $this->testPage('GET', '/');
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
