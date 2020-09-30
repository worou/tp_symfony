<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\Mime\Email;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 * 
 */
class ArticleController extends AbstractController
{
     private $security;

     public function __construct(Security $security)
     {
         $this->security = $security;
     }
     
    /**
     * @Route("/article", name="article_add")
     */
    public function index(EntityManagerInterface $em, Request $request)
    {
       
       // $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('warning', 'Vous n\'avez pas accès à cette ressource.');
            return $this->redirectToRoute("login");
        }
        
        $article = new Article();
        $form = $this->createFormBuilder($article)
                    ->add('titre', TextType::class,['attr'=>
                    ['placeholder'=>'Entrer le titre'],'label'=>'Titre de l\'article'
                    ])        
                    ->add('auteur', TextType::class,['attr'=>
                    ['placeholder'=>'Entrer l\'auteur'], 'label'=>'Auteur de l\'article'
                    ]) 
                    ->add('imageFile', FileType::class, ['attr'=>
                    ['placeholder'=>'L\'url de l\'image', 'required'=>false]
                    ])       
                    // ->add('image', UrlType::class, ['attr'=>
                    // ['placeholder'=>'L\'url de l\'image']
                    // ])        
                    ->add('description', TextareaType::class,['attr'=>
                    ['placeholder'=>'Entrer la description']
                    ])
                    ->getForm(); 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $article->setParution(new \DateTime());

            $em->persist($article);
            $em->flush();
            $this->addFlash('succes', 'Article ajouté avec succès!');
            return $this->redirectToRoute("article_list");
        }
              
        return $this->render('article/index.html.twig', [
            'formArticle'=>$form->createView()
        ]);

    }
    /**
     * @Route("/list", name="article_list")
     */
    public function list(Request $request){
       $search =  $request->request->get('search');

        $repo = $this->getDoctrine()
                    ->getRepository(Article::class);
        if($search){
            $articles =  $repo->query($search);
        }else{

            $articles = $repo->findAll();
        }
       
        return $this->render('article/list.html.twig',[
            'articles'=>$articles
        ]);
    }

    /**
     * @Route("/search", name="article_search")
     */
    public function search(ArticleRepository $repo){
      $articles = $repo->query('totot');
      dd($articles);
    }

    /**
     * @Route("/update/{id}",name="article_update")
     */
    public function update(Request $request, Article $article, EntityManagerInterface $em){

        // $em = $this->getDoctrine()->getManager();
        // $article = $em->getRepository(Article::class)
        //     ->find($id);
        //$article = $repo->find($id);
        // $this->addFlash('succes','Article modifié avec succès');
        // $article->setTitre('Modification de titre avec parameter convertor');
        // $em->flush();
        // return $this->redirectToRoute('article_list');
        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('succes', 'Article modifié avec succès!');
            return $this->redirectToRoute("article_list");
        }

        return $this->render('article/edit.html.twig',[
            'form_edit'=>$form->createView()
        ]);

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

    /**
     * @Route("/email")
     */
    public function sendEmail(MailerInterface $mailer)
    {
        $str="toto";
        $email = (new Email())
            ->from('adimicool@gmail.com')
            ->to('adimicool@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html("<p> $str See Twig integration for better HTML integration!</p>");

        $mailer->send($email);

        return new Response("email is sent");
    }
}
