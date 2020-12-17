<?php

namespace App\Controller;

use Exception;
use App\Entity\Video;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\FigureType;
use App\Form\CommentType;
use App\Form\ProfileType;
use App\Helpers\Slugify;
use Doctrine\ORM\EntityManager;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use App\Repository\PictureRepository;
use App\Service\CommentHandling;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpFoundation\File\Exception\FormSizeFileException;
use App\Service\PictureHandling;
use App\Service\VideoHandling;



class AppController extends AbstractController
{
    const NEXT_FIGURES_MAX_RESULTS = 6;

    /**
     * @Route("/", name="home")
     */
    public function index(
        FigureRepository $repo,
        SerializerInterface $serializer
    ) {
        $figures = $repo->findActiveFigures();

        return $this->render('app/index.html.twig', [
            'figures' => $figures,
            'user' => $serializer->serialize($this->getUser(), 'json', [
                'groups' => 'user_read',
            ]),
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(EntityManagerInterface $em, Request $request, PictureHandling $pictureHandling)
    {
        $user = $this->getUser();
        $pictures = [];
        $oldPicture = $pictureHandling->userHasPicture($user);
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pictures[] = $form->get('pictures')->getData();
            [$oldPicture, $errors] = $pictureHandling->handlePictures($pictures, $user);
            if (!empty($errors[0])) {
                $this->addFlash('danger', $errors[0]);
            } else {
                $em->flush();
                $this->addFlash('message', "Image modifiée avec succés");
            }
            return $this->redirectToRoute('profile');
        }
        return $this->render('app/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'oldPicture' => $oldPicture
        ]);
    }

}
