<?php

namespace App\Controller;

use App\Repository\ChapterRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @var ChapterRepository
     */
    private $repository;
    private $em;

    public function __construct(ChapterRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     * @return Response
     */
    
    public function index(ChapterRepository $repository): Response
    {
        $chapters = $repository->findAll();
        return $this->render('pages/home.html.twig', [
            'chapters' => $chapters
        ]);
    }
}