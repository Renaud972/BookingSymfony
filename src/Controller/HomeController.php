<?php

// namespace : chemin du Controller
namespace App\Controller; // App = Src/

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// Pour crerr une page :
// - une fonction public (classe)
// -  une route
// - une response

class HomeController extends Controller {

    /**
     * Creation de notre 1ère route
     *@Route("/", name="homepage")
     * @return void
     */
    public function home(){

        $nom=['Durand'=>'visiteur','Dupont'=>'admin','Jeanneau'=>'contributeur'];
        return $this->render('home.html.twig', ['titre'=>'Site d\'annonce !!','acces'=>'visiteur','tableau'=>$nom]);

    }

    /**
     * Affiche la page qui salut le visiteur
     * @Route("/hello/{nom}",name="hello")
     * @Route("/profil",name="hello-base")
     * @Route("/profil/{nom}/acces/{acces}",name ="hello-profil")
     * @return void
     */

    public function hello($nom="anonyme",$acces="visiteur"){ // anonyme par defaut si pas de valeur

        return $this->render('hello.html.twig',['title'=>'Page de profil','nom'=>$nom,'acces'=>$acces]);
    }



}

?>