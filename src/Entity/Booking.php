<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Le format doit être une date")
     * @Assert\GreaterThan("today",message="Ladate d'arrivée doit être ultérieure à la date du jour",groups="front")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Le format doit être une date")
     * @Assert\GreaterThan(propertyPath="startDate",message="La date de départ ne doit pas être antérieure à celle de départ")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Call appelé à chaque fois qu'on créé une reservation
     * @ORM\prePersist
     *
     * @return void
     */
    public function prePersist(){

        if(empty($this->createAt)){
            $this->createAt = new \DateTime();
        }

        if(empty($this->amount)){

            // prix de l'annonce X nb de jours

            $this->amount = $this->ad->getPrice() * $this->getDuration();

        }
    }

    // fct permettant de voir si les dates souhaitées sont reservables
    public function isBookableDays(){

        // il faut connaitres les dates souhaitées

        $notAvaibleDays = $this->ad->getNotAvaibleDays();

        // il faut connaitres les dates en cours de reservation

        $bookingDays = $this->getDays();

        // Comparaison

        $notAvaibleDays = array_map(function($day){
            return $day->format('Y-m-d');
        },$notAvaibleDays);

        $days = array_map(function($day){
            return $day->format('Y-m-d');
        },$bookingDays);

        // on retourne vrai ou faux

        foreach ($days as $day) {
            
            if(array_search($day,$notAvaibleDays) !== false) return false;

        }
        return true;

    }

    // retourne les jours de reservation non dispo
    public function getDays(){

       $resultat = range($this->startDate->getTimestamp(),$this->endDate->getTimestamp(),24*60*60);

       $days = array_map(function($dayTimestamp){

        return new \DateTime(date('Y-m-d',$dayTimestamp));

       },$resultat);

       return $days;

    }

    // Calcul du nb de jour du sejour
    public function getDuration(){

        $difference = $this->endDate->diff($this->startDate);

        return $difference->days;
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
