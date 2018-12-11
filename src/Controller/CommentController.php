<?php
namespace App\Controller;

use App\Repository\ChapterRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @var CommentRepository
     */
    private $repository;
    private $repositoryComment;
    private $em;

    public function __construct(ChapterRepository $repository, CommentRepository $repositoryComment, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->repositoryComment = $repositoryComment;
        $this->em = $em;
    }

    public function index()
    {
        $comments = $this->repositoryComment->findAll();
        $chapters = $this-> repository->findAll();

        return $this->render('chapter/show.html.twig', [
            compact('comments'),
            'chapters' => $chapters
        ]);
    }

     /**
     * @Route("/admin/signaled", name="comment.signal")
    */ 
    public function signaled() 
    {
        $comments = $this->repositoryComment->findSignaledQuery();
        $chapters = $this-> repository->findAll();

        return $this->render('admin/comment/signaled.html.twig', [
            'comments' => $comments,
            'chapters' => $chapters
        ]);
    }
}