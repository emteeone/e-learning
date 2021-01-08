<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavigationController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
    */
    public function index(): Response
    {
        return $this->redirectToRoute('app_login');
    }
    /**
     * @Route("/home", name="navigation")
    */
    public function naviguer(): Response
    {
        $user =$this->getUser();
        
        if($user && in_array('ROLE_ADMIN',$user->getRoles())){
            return $this->render('navigation/admin.html.twig');
        }
        else if($user && in_array('ROLE_ETUDIANT',$user->getRoles())){
            return $this->render('navigation/etudiant.html.twig');
        }
        else if($user && in_array('ROLE_ENSEIGNANT',$user->getRoles())){
            return $this->render('navigation/enseignant.html.twig');
        }
        return $this->redirectToRoute('app_login');
    }

}
