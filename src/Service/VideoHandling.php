<?php

namespace App\Service;

use App\Entity\Video;
use App\Entity\Figure;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class VideoHandling
{
    private $params;
    private $em;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }


    public function handleVideos($videos, Figure $figure)
    {
        $errors = [];
        foreach ($videos as $video) {
            if (!empty($video)) {
                $url = $this->checkVideoUrl($video);
                if ($url != null) {
                    $vid = new Video();
                    $vid->setFigure($figure);
                    $vid->setUrl($url);
                    $this->em->persist($vid);
                    $figure->addVideo($vid);
                } else {
                    $errors[] = " URL de la vidéo non valide. Veuillez entrer l'URL présente telle quelle dans la barre d\'adresse de votre navigateur internet.  Par ex: https://www.youtube.com/watch?v=Pq5p6zhgzlg ";
                }
            } else {
                $errors = "Il y a eu un problème lors de la création de votre figure";
            }
            return $errors;
        }
    }

    public function checkVideoUrl($video)
    {
        $url = htmlspecialchars($video->getUrl());
        $splittedUrl = explode('/', $url);

        if (
            ($splittedUrl[2] === 'www.youtube.com' ||
                $splittedUrl[2] === 'youtu.be') &&
            count($splittedUrl) < 6
        ) {
            if (preg_match('/watch/', $url)) {
                $videoId = explode('=', array_pop($splittedUrl))[1];
            } else {
                $videoId = array_pop($splittedUrl);
            }
            $url = 'https://www.youtube.com/embed/' . $videoId;
        } elseif (
            ($splittedUrl[2] === 'www.dailymotion.com' ||
                $splittedUrl[2] === 'dai.ly') &&
            count($splittedUrl) < 6
        ) {
            $videoId = array_pop($splittedUrl);
            $url = 'https://www.dailymotion.com/embed/video/' . $videoId;
        } else {
            $url = null;
        }
        return $url;
    }
}
