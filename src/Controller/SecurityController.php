<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{


    /**
     * @Route("/register", name="security_register")
     */
    public function index( Security $security,Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        if (!$security->isGranted('ROLE_ADMIN')) {
            $this->addFlash('warning', 'Vous n\'avez pas accès au formulaire d\'inscription.');
            return $this->redirectToRoute("article_list");
        }
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $passCrypt = $encoder->encodePassword($user, $user->getPassword());
            //dd(strlen($passCrypt));
            $user->setPassword($passCrypt);
            $user->setRoles('ROLE_USER');
            $em->persist($user);
            $em->flush();
            
        }
        return $this->render('security/index.html.twig', [
            'form_register'=>$form->createView()
        ]);
       }

        /**
         * @Route("/login", name="login")
         */
        public function connexion(AuthenticationUtils $util){

            return $this->render('security/login.html.twig',[
                "lastUserName"=>$util->getLastUsername(),
                "error"=> $util->getLastAuthenticationError()
            ]);
        }

        
        /**
         * @Route("/logout", name="logout")
         */
        public function deconnexion(){
            
        }
    
}
