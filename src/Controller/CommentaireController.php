<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/commentaire", name="commentaire")
     */
    public function index(Request $request, ArticleRepository $repo,EntityManagerInterface $em)
    {
        //$article = $repo->find(7);

        $commentaire = new Commentaire();
        // $commentaire->setAuteur('Simon')
        //             ->setMessage('Article dépassé')
        //             ->setArticle($article);
        // $em->persist($commentaire);
        // $em->flush();
        
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($commentaire);
            $em->flush();
            $this->addFlash('succes','Commentaire ajouté');
            return $this->redirectToRoute('list_comment_all');
        }
        return $this->render('commentaire/index.html.twig', [
            'formComment'=>$form->createView()
        ]);
    }

    /**
     * @Route("/list_c", name="list_comment_all")
     */
    public function list(CommentaireRepository $repo){
        $commentaires = $repo->findAll();
        //dd($commentaires);
        return $this->render('commentaire/list.html.twig',[
            'commentaires'=>$commentaires]);
    }

    /**
     * @Route("/update_c/{id}", name="update_comment")
     */
    public function update(Request $request,Commentaire $comment, EntityManagerInterface $em){

        // $comment->setAuteur('Duhamel')
        //         ->setMessage('Cet article dit vrai...');
        // $em->flush();
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
        $this->addFlash('succes','commentaire Modifé avec succes');
            return $this->redirectToRoute('list_comment_all');

        }
        return $this->render('commentaire/editComment.html.twig',['form_modifComment'=>$form->createView()]);
    }

    
    /**
     * @Route("/delete_c/{id}", name="delete_comment")
     */
    public function delete(Commentaire $comment, EntityManagerInterface $em){

        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('list_comment');
    }

    /**
     * @Route("/art_c/{id}", name="list_comment")
     */
    public function commentByPost($id,ArticleRepository $repo1,CommentaireRepository $repo){
        
        $commentaires = $repo->getCommentaireByArticle($id);
        $article = $repo1->find($id);
       
        //dd($commentaires);

        if(!$commentaires){
            $this->addFlash('succes',"Pas de commentaires de cet article.");
            return $this->redirectToRoute("article_list");
        }

        return $this->render('commentaire/art_comment.html.twig',[
            'commentaires'=>$commentaires, 'article'=>$article
        ]);
    }
}
