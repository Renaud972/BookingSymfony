<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher une page connexion
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();

        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'hasError'=>$error!==null,
            'username'=>$username
        ]);
    }

    /**
     * @Route("/logout",name="account_logout")     *
     * @return void
     */
    public function logout(){
        // il n' a besoin de rien , tout se passe via security.yaml
    }

    /**
     * Permet d'afficher une page s'incrire
     * @Route("/register",name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder,ObjectManager $manager){

        $user = new User();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $hash = $encoder->encodePassword($user,$user->getHash());

            // on modifie le mot de passe avec le setter

            $user->setHash($hash);

            $manager->persist($user);

            $manager->flush();

            $this->addFlash("success","Votre compte à bien été créé");

            return $this->redirectToRoute("account_login");

        }

        return $this->render("account/register.html.twig",['form'=>$form->createView()]);
    }

   /**
    * Modification du profil utilisateur
    *
    * @Route("account/profile",name="account_profile")
    * @return Response
    */
    public function profile(Request $request,ObjectManager $manager){

        $user = $this->getUser(); //Recupère les données de l'utilisateur connecté

        $form = $this->createForm(AccountType::class,$user);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);

            $manager->flush();

            $this->addFlash("success","Les informations de votre profil ont bien été mis à jour");
        }

        return $this->render('account/profile.html.twig',['form'=>$form->createView()]);
    }




    /**
     * Permet de modifier le mdp
     * @Route("/account/update-password",name="account_password")
     *
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager){

        $passwordUpdate = new PasswordUpdate();

        $user=$this->getUser();//

        $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //verif mot de passe actuel soit le bon

            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash())){

                //message erreur
                //$this->addFlash("warning","Mot de passe incorrect !");

                //message personalisé
                $form->get('oldPassword')->addError(new FormError("Mot de passe incorrect !"));

            }else{

                //recupere le nouveau mdp
                $newPassword = $passwordUpdate->getNewPassword();

                //on crypte le nouveau mdp
                $hash = $encoder->encodePassword($user,$newPassword);

                // on modifie le nouveau mdp ds le setter
                $user->setHash($hash);

                // on enregistre
                $manager->persist($user);

                $manager->flush();

                //on ajoute un message Flash
                $this->addFlash("success","Votre nouveau mot de passe a bien été mis à jour");

                // on redirige

                return $this->redirectToRoute('account_profile');            
            }

        }

        return $this->render('account/password.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet d'afficher la page mon compte
     * @Route("/account",name="account_home")
     * 
     * @return Response
     */
    public function myAccount(){

        return $this->render("user/index.html.twig",['user'=>$this->getUser()]);
    }

}
