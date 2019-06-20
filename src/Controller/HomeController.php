<?php

// namespace : chemin du Controller
namespace App\Controller; // App = Src/

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AdRepository;
use App\Repository\UserRepository;

// Pour créer une page :
// - une fonction public (classe)
// -  une route
// - une response

class HomeController extends AbstractController {

    /**
     * Creation de notre 1ère route/redirection auto ners les annonces
     *@Route("/", name="homepage")
     * @return void
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo){

        return $this->render('home.html.twig',
                            [
                            'ads'=>$adRepo->findBestAds(6),
                            'users'=>$userRepo->findBestUsers()

                            ]);

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