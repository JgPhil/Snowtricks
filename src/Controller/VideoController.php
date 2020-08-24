<?php

namespace App\Controller;


use App\Entity\Video;
use App\Entity\Figure;
use App\Service\VideoHandling;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideoController extends AbstractController
{
    /**
     * @Route("/figure/{figureId}/update/oldVideo/{oldVideoId}", name="video_edit")
     */
    public function updateVideo($figureId, $oldVideoId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $videoRepo = $this->getDoctrine()->getRepository(Video::class);
        $figureRepo = $this->getDoctrine()->getRepository(Figure::class);
        $figure = $figureRepo->find($figureId);

        $newVideoUrl = $request->getContent();

        if (!empty($newVideoUrl)) {
            $video = new Video();
            $video->setUrl($newVideoUrl);
            $url = $this->checkVideoUrl($video);
            if ($url !== null) {
                $video->setFigureId($figureId);
                $figure->addVideo($video);
                $em->persist($video);

                // Effacement de l'ancienne vidéo
                $em->remove($videoRepo->find($oldVideoId));
                $em->flush();
                return $this->json(
                    [
                        'newVideoUrl' => $newVideoUrl,
                        'message' => 'Video mise à jour',
                    ],
                    200
                );
            }
        } else {
            return $this->json(
                [
                    'newVideoUrl' => $newVideoUrl,
                    'error' =>
                    "Il semble qu'il y ait un problème avec cette l'url",
                ],
                404
            );
        }
    }


    private function checkVideoUrl(Video $video)
    {
        $url = htmlspecialchars($video->getUrl());
        $splittedUrl = explode('/', $url);

        if ($splittedUrl[2] === 'www.youtube.com' || $splittedUrl[2] === 'youtu.be') {
            if (preg_match('/watch/', $url)) {
                $videoId = explode('=', array_pop($splittedUrl))[1];
            } else {
                $videoId = array_pop($splittedUrl);
            }
            $url = 'https://www.youtube.com/embed/' . $videoId;
        } else if ($splittedUrl[2] === 'www.dailymotion.com' || $splittedUrl[2] === 'dai.ly') {
            $videoId = array_pop($splittedUrl);
            $url = 'https://www.dailymotion.com/embed/video/' . $videoId;
        } else {
            $url = null;
        }
        return $url;
    }
}
