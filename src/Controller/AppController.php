<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(FigureRepository $repo)
    {
        $figures = $repo->findBy(array(), array('createdAt'=> 'DESC'), 6);
        $lastFigure = $figures[array_key_first($figures)];
        return $this->render('app/index.html.twig', [
            'figures' => $figures,
            'lastFigure' => $lastFigure
        ]);
    }

    /**
     * @Route("/figure/new", name="figure_create")
     */
    public function create()
    {
        return $this->render('app/create.html.twig');
    }

    /**
     * @Route("/figure/{id}", name="trick_show")
     */
    public function show(Figure $figure)
    {
        return $this->render('app/show.html.twig', [
            'figure' => $figure
        ]);
    }

    public function sliceFigures(FigureRepository $repo, $offset)
    {
        /*
        ON RECUPERE les 6 prochaines figures  avec l'offset (Id) de la dernière figure chargée précédemment
        */
        $sliceFigures = $repo->findBy(array(),array('createdAt'=> 'DESC') , 6, $offset);
        /*
        ON RETOURNE le résultet au format json 
        */
        return $this->json($sliceFigures, 200, [
            'offset' => array_key_last($sliceFigures)
        ]);
    }

    
}
