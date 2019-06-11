<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Affiche la liste des resa
     * @Route("/admin/bookings", name="admin_bookings_list")
     * 
     * @return Response
     */
    public function index(BookingRepository $repo)
    {
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $repo->findAll()
        ]);
    }

    /**
     * Edition d'une réservation
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     *
     * @return Response
     */
    public function edit(Booking $booking, Request $request, ObjectManager $manager){

        $form = $this->createForm(AdminBookingType::class,$booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration()); //recalcul du montant à payer pour la resa

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash("success","La réservation a bieb été modifiée");
        }

        return $this->render('admin/booking/edit.html.twig',[
            'booking'=>$booking,
            'form'=>$form->createView()
            ]);

    }

    /**
     * Permet de supprimer une reservation
     * @Route("/admin/booking/{id}/delete",name="admin_booking_delete")
     * @return void
     */
    public function delete(Booking $booking,ObjectManager $manager){

        $manager->remove($booking);
        $manager->flush();

        $this->addFlash("success","Réservation n°{$booking->getId()} supprimé avec succès");

        return $this->redirectToRoute('admin_bookings_list');

    }






}
