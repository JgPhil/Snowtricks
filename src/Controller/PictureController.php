<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Picture;
use App\Service\PictureHandling;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PictureController extends AbstractController
{


    /**
     * @Route("/figure/{figureId}/update/oldPicture/{oldPictureId}/oldPictureOrder/{oldPictureOrder}", name="picture_edit")
     */
    public function updatePicture(
        $figureId,
        $oldPictureId,
        $oldPictureOrder,
        Request $request,
        PictureHandling $pictureHandling
    ) {
        $em = $this->getDoctrine()->getManager();
        $pictureRepo = $this->getDoctrine()->getRepository(Picture::class);
        $figureRepo = $this->getDoctrine()->getRepository(Figure::class);
        $figure = $figureRepo->find($figureId);        
        $pictureOrder = $pictureHandling->getFigurePictureSortOrder($oldPictureId, $pictureRepo, $oldPictureOrder);
        //Récupération et sauvegarde du fichier image
        $picture = $request->files->get('file');
        $filename = $pictureHandling->movePicture($picture);
        $newPicture = new Picture();
        $newPicture->setSortOrder($pictureOrder);
        $newPicture->setFigure($figure);
        $newPicture->setName($filename);

        $figure->addPicture($newPicture);
        $em->flush();

        return $this->json(
            [
                'message' => 'Image mise à jour',
                'newPictureFilename' => $filename,
            ],
            200
        );
    }
}
