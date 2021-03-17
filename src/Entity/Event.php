<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * 
 * @ORM\HasLifecycleCallbacks()
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("home_events")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     */
    private $authorName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     */
    private $troopName;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     */
    private $eventDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     */
    private $troopDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * 
     * @Groups("home_events")
     */
    private $reservationContact;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     */
    private $image;

    /**
     * @ORM\Column(type="float", nullable=true)
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     * @Assert\Type(type="float")
     */
    private $fullPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     * 
     * @Groups("home_events")
     * @Assert\Type(type="float")
     * 
     */
    private $reducedPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     * 
     * @Groups("home_events")
     * @Assert\Type(type="float")
     */
    private $subscriberPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     * 
     * @Groups("home_events")
     * @Assert\Type(type="float")
     */
    private $childrenPrice;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="events")
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Place::class, inversedBy="events")
     * 
     * @Groups("home_events")
     * 
     * @Assert\NotBlank
     */
    private $place;

    /**
     * @ORM\OneToMany(targetEntity=Date::class, cascade={"persist", "remove"}, mappedBy="event")
     * 
     * @Assert\NotBlank
     * 
     * @Groups("home_events")
     */
    private $dates;

    /**
     * @ORM\OneToMany(targetEntity=DayOff::class, cascade={"persist", "remove"}, mappedBy="event")
     * 
     * @Groups("home_events")
     * 
     */
    private $dayOffs;

    /**
     * @ORM\OneToMany(targetEntity=Alert::class, mappedBy="event")
     * 
     */
    private $alerts;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="events")
     * 
     * @Groups("home_events")
     * @Assert\NotBlank
     */
    private $user;

    public function __construct()
    {
        $this->dates = new ArrayCollection();
        $this->dayOffs = new ArrayCollection();
        $this->alerts = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
    * @ORM\PrePersist
    */
    public function setCreatedAtVAlue()
    {
        $this->createdAt = new \DateTime();
    }

    /**
    * @ORM\PreUpdate
    */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(?string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getTroopName(): ?string
    {
        return $this->troopName;
    }

    public function setTroopName(?string $troopName): self
    {
        $this->troopName = $troopName;

        return $this;
    }

    public function getEventDescription(): ?string
    {
        return $this->eventDescription;
    }

    public function setEventDescription(?string $eventDescription): self
    {
        $this->eventDescription = $eventDescription;

        return $this;
    }

    public function getTroopDescription(): ?string
    {
        return $this->troopDescription;
    }

    public function setTroopDescription(?string $troopDescription): self
    {
        $this->troopDescription = $troopDescription;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(?string $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getReservationContact(): ?string
    {
        return $this->reservationContact;
    }

    public function setReservationContact(?string $reservationContact): self
    {
        $this->reservationContact = $reservationContact;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getFullPrice()
    {
        return $this->fullPrice;
    }

    public function setFullPrice($fullPrice): self
    {
        $this->fullPrice = $fullPrice;

        return $this;
    }

    public function getReducedPrice(): ?float
    {
        return $this->reducedPrice;
    }

    public function setReducedPrice(?float $reducedPrice): self
    {
        $this->reducedPrice = $reducedPrice;

        return $this;
    }

    public function getSubscriberPrice(): ?float
    {
        return $this->subscriberPrice;
    }

    public function setSubscriberPrice(?float $subscriberPrice): self
    {
        $this->subscriberPrice = $subscriberPrice;

        return $this;
    }

    public function getChildrenPrice(): ?float
    {
        return $this->childrenPrice;
    }

    public function setChildrenPrice(?float $childrenPrice): self
    {
        $this->childrenPrice = $childrenPrice;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getType(): ?type
    {
        return $this->type;
    }

    public function setType(?type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPlace(): ?place
    {
        return $this->place;
    }

    public function setPlace(?place $place): self
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return Collection|Date[]
     */
    public function getDates(): Collection
    {
        return $this->dates;
    }

    public function addDate(Date $date): self
    {
        if (!$this->dates->contains($date)) {
            $this->dates[] = $date;
            $date->setEvent($this);
        }

        return $this;
    }

    public function removeDate(Date $date): self
    {
        if ($this->dates->removeElement($date)) {
            // set the owning side to null (unless already changed)
            if ($date->getEvent() === $this) {
                $date->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DayOff[]
     */
    public function getDayOffs(): Collection
    {
        return $this->dayOffs;
    }

    public function addDayOff(DayOff $dayOff): self
    {
        if (!$this->dayOffs->contains($dayOff)) {
            $this->dayOffs[] = $dayOff;
            $dayOff->setEvent($this);
        }

        return $this;
    }

    public function removeDayOff(DayOff $dayOff): self
    {
        if ($this->dayOffs->removeElement($dayOff)) {
            // set the owning side to null (unless already changed)
            if ($dayOff->getEvent() === $this) {
                $dayOff->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Alert[]
     */
    public function getAlerts(): Collection
    {
        return $this->alerts;
    }

    public function addAlert(Alert $alert): self
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts[] = $alert;
            $alert->setEvent($this);
        }

        return $this;
    }

    public function removeAlert(Alert $alert): self
    {
        if ($this->alerts->removeElement($alert)) {
            // set the owning side to null (unless already changed)
            if ($alert->getEvent() === $this) {
                $alert->setEvent(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


}
