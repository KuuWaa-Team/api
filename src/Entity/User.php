<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Auth\LoginController;
use App\Controller\Event\ListingUserEventController;
use App\Controller\Wallet\ListingOperationsController;
use App\Controller\Wallet\RechargeController;
use App\Entity\Event\EventAdmin;
use App\Entity\Wallet\Operation;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/login',
            requirements: [
                'email' => 'String', 'phoneId' => 'String', 'displayName' => 'String', 'provider' => 'String'
            ],
            controller: LoginController::class,
            description: "Login with google",
        ),
        new GetCollection(
            uriTemplate: '/users/{id}/events',
            controller: ListingUserEventController::class,
            normalizationContext: [
                'groups' => ['read:events']
            ]
        ),
        new GetCollection(
            uriTemplate: '/users/{id}/operations',
            controller: ListingOperationsController::class,
            normalizationContext: [
                'groups' => ['operation:pRead']
            ]
        ),
        new Post(
            uriTemplate: '/users/{id}/recharge',
            requirements: [
                'amount' => 'decimal', 'payment_method' => 'String'
            ],
            controller: RechargeController::class,
            description: "Recharge credits",
        ),
    ]
)]
class User implements UserInterface
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:pRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:pRead'])]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:pRead'])]
    private ?int $credits = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Groups(['user:pRead'])]
    private ?string $displayName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:pRead'])]
    private ?string $photoUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:pRead'])]
    private ?string $provider = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:pRead'])]
    private ?string $uid = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Event::class)]
    private Collection $events;

    /**
     * @var Collection<int, Operation>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Operation::class)]
    private Collection $operations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneId = null;

    /**
     * @var Collection<int, EventAdmin>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EventAdmin::class)]
    private Collection $admins;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->operations = new ArrayCollection();
        $this->admins = new ArrayCollection();
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

    public function getCredits(): ?int
    {
        return $this->credits;
    }

    public function setCredits(?int $credits): static
    {
        $this->credits = $credits;

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

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): static
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setOwner($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            if ($event->getUser() === $this) {
                $event->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): static
    {
        if (!$this->operations->contains($operation)) {
            $this->operations->add($operation);
            $operation->setUser($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): static
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getUser() === $this) {
                $operation->setUser(null);
            }
        }

        return $this;
    }

    public function getPhoneId(): ?string
    {
        return $this->phoneId;
    }

    public function setPhoneId(?string $phoneId): static
    {
        $this->phoneId = $phoneId;

        return $this;
    }

    /**
     * @return Collection<int, EventAdmin>
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(EventAdmin $admin): static
    {
        if (!$this->admins->contains($admin)) {
            $this->admins->add($admin);
            $admin->setUser($this);
        }

        return $this;
    }

    public function removeAdmin(EventAdmin $admin): static
    {
        if ($this->admins->removeElement($admin)) {
            // set the owning side to null (unless already changed)
            if ($admin->getUser() === $this) {
                $admin->setUser(null);
            }
        }

        return $this;
    }
}
