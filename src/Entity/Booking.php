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
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date d'arrivée doit être au bon format")
     * @Assert\GreaterThan("today UTC +5 hours", message="La date de départ doit être supérieur à aujourd'hui", groups={"front"})
     */
    public $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date de départ doit être au bon format")
     * @Assert\Expression("value > this.startDate", message="Vous devez entrer une date de fin supérieure à la date de départ")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Callback appelé a chaque fois qu'on réserve
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @throws \Exception
     */
    public function prePersist() {
        if(empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }

        if (empty($this->amount)) {
            $this->amount = $this->ad->getPrice()*$this->getDuration();
        }
    }

    public function isBookableDate() {
        // Il faut connaitre les dates impossibles pour l'annonce
        $notAvailableDays = $this->ad->getNotAvailableDays();
        //Il faut comparer les dates choisies avec les dates impossibles
        $bookingDays = $this->getDays();

        $formatDay = function($day){
            return $day->format('y-m-d');
        };

        $days = array_map($formatDay, $bookingDays);

        $notAvailable = array_map($formatDay, $notAvailableDays);

        foreach ($days as $day) {
            if(array_search($day, $notAvailable) !== false )return false;
        }
        //on retourne true si c 'est good
        return true;
    }


    /**
     * Permet de récupérer un tablea des journées qui correspondent à la réservation en cours
     */
    public function getDays() {
        $resultat = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            10 * 60 * 60
        );

        $days = array_map(function($dayTimestamp) {
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $resultat);
        return $days;
    }

    public function getDuration() {
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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
