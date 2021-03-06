<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Services\Pagination;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{

    // 1ère méthode de recup des données ds la bdd puis render ds la view/affiche de toutes les annonces
    // public function index(){
    //     $repo = $this->getDoctrine()->getRepository(Ad::class);

    //     //via $repo, on va chercher toute les annonces.

    //     $ads = $repo->findAll();
        
    //     return $this->render('ad/index.html.twig', [
    //         'controller_name' => 'Nos annonces',
    //         'ads'=>$ads
    //     ]);
    // }
    

     /**
     * @Route("/ads/{page<\d+>?1}", name="ads_list")
     */
        // 2ème méthode de recup des données ds la bdd puis render ds la view : instenciation ds les param de la fct
    public function index($page, Pagination $paginationService){
        //via $repo, on va chercher toute les annonces.
        //$ads = $repo->findAll();

        $paginationService->setEntityClass(Ad::class)
                          ->setPage($page)
                          //->setRoute('admin_ads_list')
                          ->setLimit(9)
                          ;


        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Nos annonces',
            'pagination'=>$paginationService
        ]);
    }


    /**
     * Création d'annonce
     * @Route("/ads/new",name="ads_create")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function create(Request $request,ObjectManager $manager){

        // fabrication de formulaire : FORMBUILDER

        $ad = new Ad();
        
        //on lance la fabrication de notre formuliare

        $form = $this->createForm(AnnonceType::class,$ad);

        $form -> handleRequest($request);// recupere les données de la methode "post" du form
                
        //validation du form
        if($form->isSubmitted() && $form->isValid()){

            // si ok, on demande à Doctrine de sauvegarder ds la bdd 

            // ces données dans l'objet $manager

            // pour chaque image supplementaire ajouté

            foreach($ad->getImages() as $image){

                // relie l'image à l'annonce et on mofifie l'annonce
                $image->setAd($ad);

                // on sauvegarde les images

                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());
            $manager->persist($ad);
            $manager->flush();

            
            // message flash
            $this->addFlash('success',"Annonce <strong>{$ad->getTitle()}</strong> créée avec succès");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }

        
        return $this->render('ad/new.html.twig',['form'=>$form->createView()]);

    }

    // mettre les routes avec paramétres à la fin

    /**
     * Permet d'afficher une seule annonce
     * @Route("/ads/{slug}", name="ads_single")
     *
     * @return Response
     */
    public function show(Ad $ad){
        // recuperation de l'annonce qui correspond au Slug
        // X = un champ de la table à preciser à la place de X
       
       // $ad = $repo->findOneBySlug($slug); // Première méthode

        return $this->render('ad/show.html.twig',['ad'=>$ad]);
    }

    /**
     * Permet d'éditer et de modifier un article
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",message="Cette annonce ne vous appartient pas, vous ne pouvez la modifier")
     *
     * @return Response
     */
    public function edit(Ad $ad,Request $request,ObjectManager $manager){

        $form = $this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);
        

        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getImages() as $image){

                $image->setAd($ad);

                $manager->persist($image);
            }

            $manager->persist($ad);

            $manager->flush();

            $this->addFlash("success", "les modifications ont été faites !");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig',['form'=>$form->createView(),'ad'=>$ad]);

    }
    /** Permet la suppression d'une annonce
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted(ROLE_USER) and user == ad.getAuthor()",message="Vous ne pouvez accéder à cette ressource !!!")
     * 
     * @return Response
     */
    public function delete(ObjectManager $manager, Ad $ad){

        $manager->remove($ad);
        $manager->flush();
        $this->addFlash("success","L'annonce <em>{$ad->getTitle()}</em> a bien été supprimée");

        return $this->redirectToRoute("account_profile");

    }








}
