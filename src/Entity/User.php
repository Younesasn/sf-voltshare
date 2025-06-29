<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\MeController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new Get(
            name: 'me',
            uriTemplate: '/me',
            controller: MeController::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            read: false,
            output: User::class,
            normalizationContext: ['groups' => ['user:read']],
        ),
        new Post(),
        new Patch(),
    ]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private ?string $tel = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $code = null;

    /**
     * @var Collection<int, Car>
     */
    #[ORM\OneToMany(targetEntity: Car::class, mappedBy: 'user')]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private Collection $cars;

    /**
     * @var Collection<int, Station>
     */
    #[ORM\OneToMany(targetEntity: Station::class, mappedBy: 'user')]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private Collection $stations;

    /**
     * @var Collection<int, Station>
     */
    #[ORM\ManyToMany(targetEntity: Station::class, inversedBy: 'usersStarred')]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private Collection $stationStarred;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user')]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private Collection $reservations;

    #[ORM\Column]
    private ?bool $isDeleted = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'conversation:read', "conversation:write"])]
    private ?string $avatar = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'host')]
    private Collection $hostConversations;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'customer')]
    private Collection $customerConversations;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
        $this->stations = new ArrayCollection();
        $this->stationStarred = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->hostConversations = new ArrayCollection();
        $this->customerConversations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    function getEmailAuthCode(): ?string
    {
        return $this->code;
    }

    function setEmailAuthCode(string $authCode): void
    {
        $this->code = $authCode;
    }

    function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    function isEmailAuthEnabled(): bool
    {
        return true;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setUser($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getUser() === $this) {
                $car->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Station>
     */
    public function getStations(): Collection
    {
        return $this->stations;
    }

    public function addStation(Station $station): static
    {
        if (!$this->stations->contains($station)) {
            $this->stations->add($station);
            $station->setUser($this);
        }

        return $this;
    }

    public function removeStation(Station $station): static
    {
        if ($this->stations->removeElement($station)) {
            // set the owning side to null (unless already changed)
            if ($station->getUser() === $this) {
                $station->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Station>
     */
    public function getStationStarred(): Collection
    {
        return $this->stationStarred;
    }

    public function addStationStarred(Station $stationStarred): static
    {
        if (!$this->stationStarred->contains($stationStarred)) {
            $this->stationStarred->add($stationStarred);
        }

        return $this;
    }

    public function removeStationStarred(Station $stationStarred): static
    {
        $this->stationStarred->removeElement($stationStarred);

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
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getHostConversations(): Collection
    {
        return $this->hostConversations;
    }

    public function addHostConversation(Conversation $hostConversation): static
    {
        if (!$this->hostConversations->contains($hostConversation)) {
            $this->hostConversations->add($hostConversation);
            $hostConversation->setHost($this);
        }

        return $this;
    }

    public function removeHostConversation(Conversation $hostConversation): static
    {
        if ($this->hostConversations->removeElement($hostConversation)) {
            // set the owning side to null (unless already changed)
            if ($hostConversation->getHost() === $this) {
                $hostConversation->setHost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getCustomerConversations(): Collection
    {
        return $this->customerConversations;
    }

    public function addCustomerConversation(Conversation $customerConversation): static
    {
        if (!$this->customerConversations->contains($customerConversation)) {
            $this->customerConversations->add($customerConversation);
            $customerConversation->setCustomer($this);
        }

        return $this;
    }

    public function removeCustomerConversation(Conversation $customerConversation): static
    {
        if ($this->customerConversations->removeElement($customerConversation)) {
            // set the owning side to null (unless already changed)
            if ($customerConversation->getCustomer() === $this) {
                $customerConversation->setCustomer(null);
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'email' => $this->getEmail(),
        ];
    }
}
