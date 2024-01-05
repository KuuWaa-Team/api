<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Event\ActivePackEventController;
use App\Controller\Event\CreateEventController;
use App\Controller\Event\ListingEventInvitationsController;
use App\Controller\Event\ListingUserEventController;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            name: "userInvitations",
            uriTemplate: "/events/{id}/invitations",
            controller: ListingEventInvitationsController::class,
            normalizationContext: [
                'groups' => ['read:event:invitations']
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => ['read:events', 'read:event', 'read:event:invitations']
            ]
        ),
        new Post(
            name: 'activePack',
            uriTemplate: '/events/{id}/active-pack',
            description: 'Active pack for event',
            controller: ActivePackEventController::class,
            openapiContext: [
                'summary' => 'create order transaction',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'eventPack' => [
                                        'type' => EventPack::class,
                                        'example' => 'https://example.com/',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
                'responseBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => []
                            ]
                        ]
                    ]
                ]
            ],
        ),
        new GetCollection(
            controller: ListingUserEventController::class,
            normalizationContext: [
                'groups' => ['read:events']
            ]
        ),
        new Post(
            controller: CreateEventController::class,
            openapiContext: [
                'summary' => 'create order transaction',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => [
                                        'type' => 'string',
                                        'example' => 'Mariage de Joe',
                                    ],
                                    'date' => [
                                        'type' => 'datetime',
                                        'example' => '2024-01-04T09:58:22.696Z',
                                    ],
                                    'startHour' => [
                                        'type' => 'string',
                                        'example' => '2024-01-04T09:58:22.696Z'
                                    ],
                                    'endHour' => [
                                        'type' => 'string',
                                        'example' => '2024-01-04T09:58:22.696Z'
                                    ],
                                    'details' => [
                                        'type' => 'string',
                                        'example' => 'lorem ipsum'
                                    ],
                                    'address' => [
                                        'type' => 'string',
                                        'example' => 'Kinshasa - Gombe'
                                    ],
                                    'category' => [
                                        'type' => EventCategory::class,
                                        'example' => 'https://example.com/'
                                    ],
                                    'user' => [
                                        'type' => User::class,
                                        'example' => 'https://example.com/'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'responseBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => []
                            ]
                        ]
                    ]
                ]
            ],
        )
    ]
)]
#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:events'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:events'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:events'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['read:events'])]
    private ?\DateTimeInterface $startHour = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    #[Groups(['read:events'])]
    private ?\DateTimeInterface $endHour = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:event'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:event'])]
    private ?string $details = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(['read:events'])]
    private ?EventCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Invitation::class)]
    #[Groups(['read:event:invitations'])]
    private Collection $invitations;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Table::class)]
    #[Groups(['read:event'])]
    private Collection $tables;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:events'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(['read:event'])]
    private ?EventPack $eventPack = null;

    public function __construct()
    {
        $this->invitations = new ArrayCollection();
        $this->tables = new ArrayCollection();
        $this->setStatus("pending");
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStartHour(): ?\DateTimeInterface
    {
        return $this->startHour;
    }

    public function setStartHour(\DateTimeInterface $startHour): static
    {
        $this->startHour = $startHour;

        return $this;
    }

    public function getEndHour(): ?\DateTimeInterface
    {
        return $this->endHour;
    }

    public function setEndHour(?\DateTimeInterface $endHour): static
    {
        $this->endHour = $endHour;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getCategory(): ?EventCategory
    {
        return $this->category;
    }

    public function setCategory(?EventCategory $category): static
    {
        $this->category = $category;

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
     * @return Collection<int, Invitation>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): static
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setEvent($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getEvent() === $this) {
                $invitation->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Table>
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Table $table): static
    {
        if (!$this->tables->contains($table)) {
            $this->tables->add($table);
            $table->setEvent($this);
        }

        return $this;
    }

    public function removeTable(Table $table): static
    {
        if ($this->tables->removeElement($table)) {
            // set the owning side to null (unless already changed)
            if ($table->getEvent() === $this) {
                $table->setEvent(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getEventPack(): ?EventPack
    {
        return $this->eventPack;
    }

    public function setEventPack(?EventPack $eventPack): static
    {
        $this->eventPack = $eventPack;

        return $this;
    }
}
