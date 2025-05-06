<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\StationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource()]
#[ORM\Entity(repositoryClass: StationRepository::class)]
// ?timeslots.weekday=mercredi&reservations.date=2025-04-23
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'timeslots.weekday' => 'exact', 'reservations.date' => 'exact'])]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?float $longitude = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $picture = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?float $price = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?float $power = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['user:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'stations')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    /**
     * @var Collection<int, Timeslot>
     */
    #[ORM\OneToMany(targetEntity: Timeslot::class, mappedBy: 'station')]
    #[Groups(['user:read'])]
    private Collection $timeslots;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'stationStarred')]
    #[Groups(['user:read'])]
    private Collection $usersStarred;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'station')]
    #[Groups(['user:read'])]
    private Collection $reservations;

    public function __construct()
    {
        $this->timeslots = new ArrayCollection();
        $this->usersStarred = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPower(): ?float
    {
        return $this->power;
    }

    public function setPower(float $power): static
    {
        $this->power = $power;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Timeslot>
     */
    public function getTimeslots(): Collection
    {
        return $this->timeslots;
    }

    public function addTimeslot(Timeslot $timeslot): static
    {
        if (!$this->timeslots->contains($timeslot)) {
            $this->timeslots->add($timeslot);
            $timeslot->setStation($this);
        }

        return $this;
    }

    public function removeTimeslot(Timeslot $timeslot): static
    {
        if ($this->timeslots->removeElement($timeslot)) {
            // set the owning side to null (unless already changed)
            if ($timeslot->getStation() === $this) {
                $timeslot->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersStarred(): Collection
    {
        return $this->usersStarred;
    }

    public function addUsersStarred(User $usersStarred): static
    {
        if (!$this->usersStarred->contains($usersStarred)) {
            $this->usersStarred->add($usersStarred);
            $usersStarred->addStationStarred($this);
        }

        return $this;
    }

    public function removeUsersStarred(User $usersStarred): static
    {
        if ($this->usersStarred->removeElement($usersStarred)) {
            $usersStarred->removeStationStarred($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setStation($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getStation() === $this) {
                $reservation->setStation(null);
            }
        }

        return $this;
    }
}
