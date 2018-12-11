<?php 

namespace App\Controller\Admin;

use App\Entity\Chapter;
use App\Entity\Comment;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminChapterController extends AbstractController
{
    /**
     * @var ChapterRepository
     */
    private $repository;
    private $repositoryComment;
    private $em;

    public function __construct(ChapterRepository $repository,CommentRepository $repositoryComment, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->repositoryComment = $repositoryComment;
        $this->em = $em;
    }
    /**
     * @Route("/admin", name="admin.chapter.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $chapters = $this-> repository->findAll();
        
        return $this->render('admin/chapter/index.html.twig', compact('chapters'));
    }

    /**
     * @Route("/admin/chapter/create", name="admin.chapter.new")
     */
    public function new(Request $request) 
    {
        $chapter = new Chapter();
        $chapters = $this-> repository->findAll();
        
        $form = $this-> createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($chapter);
            $this->em->flush();
            $this->addFlash('success', 'Le nouveau chapitre a bien été créé!');
            return $this->redirectToRoute('admin.chapter.index');
        }

        return $this->render    ('admin/chapter/new.html.twig', [
            'chapter' => $chapter,
            'chapters' => $chapters,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/chapter/{id}", name="admin.chapter.edit", methods="GET|POST")
     */
    public function edit(Chapter $chapter, Request $request)
    {
        $chapters = $this-> repository->findAll();
        $form = $this-> createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Le chapitre a bien été modifié!');
            return $this->redirectToRoute('admin.chapter.index');
        }

        return $this->render('admin/chapter/edit.html.twig', [
            'chapter' => $chapter,
            'chapters' =>$chapters,
            'form' => $form->createView()
        ]);
    }

    /**
     *@Route("/admin/chapter/{id}", name= "admin.chapter.delete", methods="DELETE")
     */
    public function delete(Chapter $chapter, Request $request)
    {
        $chapters = $this-> repository->findAll();

        if ($this->isCsrfTokenValid('delete' . $chapter->getId(), $request->get('_token'))) {
            $this->em->remove($chapter);
            $this->em->flush();
            $this->addFlash('success', 'Le chapitre a bien été supprimé!');
        }
        
        return $this->redirectToRoute('admin.chapter.index');
    }

    /**
     *@Route("/admin/signaled/{id}", name="admin.comment.delete", methods="DELETE")
     */
    public function removeComment(Comment $comment, Request $request)
    {
        $chapters = $this-> repository->findAll();

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->get('_token'))) {
            $this->em->remove($comment);
            $this->em->flush();
            $this->addFlash('success', 'Le commentaire a bien été supprimé');
        }
       
        $comments = $this->repositoryComment->findSignaledQuery();

        return $this->render('admin/comment/signaled.html.twig', [
            'comments' => $comments,
            'chapters' => $chapters
        ]);
    }

    /**
     * @Route("/admin/signaled/{id}", name="admin.comment.approve", methods="GET")
     */
    public function approveComment(Comment $comment, Request $request)
    {
        $chapters = $this-> repository->findAll();
        
        //Recupération du parametre signal dans Url
        $signal = $request->query->get('signal');
       
        if ($signal == false) {
            $idComment = $comment->getId();
           
            /*  @var \App\Entity\Comment */
            $signaledComment = $this->repositoryComment->find($idComment);
            
            $signaledComment->setSignaled(false);
            dump($signaledComment);
           
            $this->em->flush();
            
            $this->addFlash('success', 'Le commentaire a bien été approuvé');
        }
        
        $comments = $this->repositoryComment->findSignaledQuery();

        return $this->render('admin/comment/signaled.html.twig', [
            'comments' => $comments,
            'chapters' => $chapters
        ]);
    }

     /**
     * @Route("/logout_message", name="logout_message")
     */
    public function logoutMessage()
    {
        $this->addFlash('success', 'Vous êtes bien déconnecté(e)');
        return $this->redirectToRoute('home');
    }
}