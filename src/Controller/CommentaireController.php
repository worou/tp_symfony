<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/commentaire/", name="commentaire")
     */
    public function index(ArticleRepository $repo,EntityManagerInterface $em)
    {
        $article = $repo->find(7);

        $commentaire = new Commentaire();
        $commentaire->setAuteur('Simon')
                    ->setMessage('Article dépassé')
                    ->setArticle($article);
        $em->persist($commentaire);
        $em->flush();

        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }

    /**
     * @Route("/list_c", name="list_comment")
     */
    public function list(CommentaireRepository $repo){
        $commentaires = $repo->findAll();

        return $this->render('commentaire/list.html.twig',[
            'commentaires'=>$commentaires]);
    }

    /**
     * @Route("/update_c/{id}", name="update_comment")
     */
    public function update(Commentaire $comment, EntityManagerInterface $em){

        $comment->setAuteur('Duhamel')
                ->setMessage('Cet article dit vrai...');
        $em->flush();

        return $this->redirectToRoute('list_comment');
    }

    
    /**
     * @Route("/delete_c/{id}", name="delete_comment")
     */
    public function delete(Commentaire $comment, EntityManagerInterface $em){

        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('list_comment');
    }
}
