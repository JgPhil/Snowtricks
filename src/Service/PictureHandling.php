<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Figure;
use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureHandling
{

    private $params;
    private $em;
    private $acceptedExtensions = ['jpeg', 'jpg', 'gif', 'png'];

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }

    public function handlePictures($pictures, $entity)
    {
        $oldPicture = null;
        $errors = [];
        foreach ($pictures as $picture) {
            if (!empty($picture)) {
                if ($picture->getSize() < 2097150) {
                    $extension = $this->correctExtension($picture);
                    if ($extension !== null) {
                        if ($entity instanceof Figure) {
                            $pic = $this->addFigurePicture($picture, $entity);
                            $entity->addPicture($pic);
                        } else {
                            $oldPicture = $this->addUserPicture($picture, $entity);
                        }
                    } else {
                        $errors[] =  "Mauvais format d'image.  Fichiers acceptés: jpg / jpeg / gif / png ";
                    }
                } else {
                    $errors[] = "Le fichier image est trop volumineux. maximum: 2 Mb";
                }
            } else {
                $errors[] = "Il y a eu un problème lors de la création de votre figure";
            }
        }
        return [$oldPicture, $errors];
    }

    public function userHasPicture(User $user)
    {
        $oldPicture = null;
        if (count($user->getPictures()) > 0) {
            $oldPicture = $user->getPictures()[0];
        }
        return $oldPicture;
    }

    public function correctExtension($picture)
    {
        $extension = $picture->guessExtension();
        if (
            isset($extension) &&
            in_array($extension, $this->acceptedExtensions)
        ) {
            return $extension;
        }
    }

    public function addUserPicture($picture, User $user)
    {
        $oldPicture = $this->userHasPicture($user);
        if ($oldPicture !== null) {
            $this->em->remove($oldPicture);
        }
        $filename = $this->movePicture($picture);
        $pic = new Picture;
        $pic->setName($filename);
        $user->addPicture($pic);
        $pic->setUser($user);
        $this->em->persist($pic);
        return $oldPicture;
    }

    public function addFigurePicture($picture, Figure $figure)
    {
        //Vérification du champ sort_order maximum en base
        $maxOrder = $this->findPictureHighestOrder($figure);
        $filename = $this->movePicture($picture);
        $pic = new Picture;
        $pic->setName($filename);
        $pic->setSortOrder($maxOrder + 1);
        $pic->setFigure($figure);
        $this->em->persist($pic);

        return $pic;
    }


    public function getFigurePictureSortOrder($oldPictureId = null, PictureRepository $pictureRepo, $oldPictureOrder)
    {
        if ($oldPictureId != 'null') {
            //modification d'une image
            $oldPicture = $pictureRepo->find($oldPictureId);
            // effacement de de l'ancienne image dans le dossier Pictures
            unlink(
                $this->params->get('pictures_directory') .
                    '/' .
                    $oldPicture->getName()
            );
            //effacement de l'entrée en base de l'ancienne image
            $this->em->remove($oldPicture);
            $pictureOrder = $oldPictureOrder;
        } else {
            $pictureOrder = 1; // Default picture
        }
        return $pictureOrder;
    }


    private function findPictureHighestOrder(Figure $figure)
    {
        $pictures = $figure->getPictures();
        // trouver le sort_order le plus élevé dans les images
        $maxOrder = 0;
        foreach ($pictures as $picture) {
            if ($picture->getsortOrder() > $maxOrder) {
                $maxOrder = $picture->getsortOrder();
            }
        }
        return $maxOrder;
    }

    public function movePicture($picture)
    {
        //nouveau nom de fichier
        $filename = md5(uniqid()) . '.' . $picture->guessExtension();
        //copie dans dossier uploads
        $picture->move(
            $this->params->get('pictures_directory'),
            $filename
        );
        return $filename;
    }



}
