<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(EntityManagerInterface $em)
    {
        //$em = $this->getDoctrine()->getManager();
        for($i = 0; $i < 1; $i++){

            $article = new Article();
            $article->setTitre("Article nouveau $i")
                    ->setAuteur("Dupont $i")
                    ->setParution(new \DateTime())
                    ->setImage('https://via.placeholder.com/150')
                    ->setDescription("Description de l'article $i");
            $em->persist($article);
        }
        $em->flush();
              
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);

    }
    /**
     * @Route("/list", name="article_list")
     */
    public function list(){
        $repo = $this->getDoctrine()
                    ->getRepository(Article::class);
        $articles = $repo->findAll();
        //dd($articles);
        return $this->render('article/list.html.twig',[
            'articles'=>$articles
        ]);
    }

    /**
     * @Route("/search", name="article_search")
     */
    public function search(ArticleRepository $repo){
      $articles = $repo->query('m');
      dd($articles);
    }

    /**
     * @Route("/update/{id}",name="article_update")
     */
    public function update(Article $article, EntityManagerInterface $em){

        // $em = $this->getDoctrine()->getManager();
        // $article = $em->getRepository(Article::class)
        //     ->find($id);
        //$article = $repo->find($id);
        $this->addFlash('succes','Article modifié avec succès');
        $article->setTitre('Modification de titre avec parameter convertor');
        $em->flush();
        return $this->redirectToRoute('article_list');
    }

    /**
     * @Route("/delete/{id}",name="article_delete")
     */
    public function delete(EntityManagerInterface $em, ArticleRepository $repo,$id){
        // $em = $this->getDoctrine()->getManager();
        // $article = $em->getRepository(Article::class)
        //    ->find($id);
        $this->addFlash('succes','Article supprimé avec succès');
        $article = $repo->find($id);
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article_list');
    }
}
