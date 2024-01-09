<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\Metadata\Put;
use App\Controller\Event\ActivePackEventController;
use App\Controller\Event\AddInvitationEventController;
use App\Controller\Event\AddTableEventController;
use App\Controller\Event\CreateEventController;
use App\Controller\Event\ListingEventInvitationsController;
use App\Controller\Event\ListingEventTablesController;
use App\Controller\Event\ListingUserEventController;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: "/events/{id}/invitations",
            controller: ListingEventInvitationsController::class,
            normalizationContext: [
                'groups' => ['read:event:invitations']
            ],
            name: "userInvitations"
        ),
        new Get(
            uriTemplate: "/events/{id}/tables",
            controller: ListingEventTablesController::class,
            normalizationContext: [
                'groups' => ['read:event']
            ],
            name: "userTables"
        ),
        new Get(
            normalizationContext: [
                'groups' => ['event:fRead']
            ]
        ),
        new Post(
            uriTemplate: '/events/{id}/active_event_pack',
            controller: ActivePackEventController::class,
            openapiContext: [
                'summary' => 'active pack event',
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
            ],
            description: 'Active pack for event',
            name: 'activePack',
        ),
        new Post(
            uriTemplate: '/events/{id}/tables',
            controller: AddTableEventController::class,
            openapiContext: [
                'summary' => 'Add table for event',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => [
                                        'type' => 'string',
                                        'example' => 'Orange',
                                    ],
                                    'count' => [
                                        'type' => 'int',
                                        'example' => 10,
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            description: 'Add table for event',
            name: 'addTable',
        ),
        new Post(
            uriTemplate: '/events/{id}/invitations',
            controller: AddInvitationEventController::class,
            openapiContext: [
                'summary' => 'Add invitation for event',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => [
                                        'type' => 'string',
                                        'example' => 'Joella',
                                    ],
                                    'civility' => [
                                        'type' => 'string',
                                        'example' => 'Mme',
                                    ],
                                    'count' => [
                                        'type' => 'int',
                                        'example' => 1,
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            description: 'Add invitation for event',
            name: 'addInvitation',
        ),
        new GetCollection(
            controller: ListingUserEventController::class,
            normalizationContext: [
                'groups' => ['read:events']
            ]
        ),
        new Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
            controller: CreateEventController::class,
            deserialize: false
        ),
        new Put(),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $details = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?EventCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Invitation::class)]
    private Collection $invitations;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Table::class)]
    private Collection $tables;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?EventPack $eventPack = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?bool $paidEvent = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $paymentStatus = null;

    #[ORM\ManyToMany(targetEntity: Feature::class, inversedBy: 'events')]
    #[Groups(['event:fRead'])]
    private Collection $features;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $imagePath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?\DateTimeInterface $date = null;

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function __construct()
    {
        $this->invitations = new ArrayCollection();
        $this->tables = new ArrayCollection();
        $this->setStatus("pending");
        $this->features = new ArrayCollection();
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

    public function isPaidEvent(): ?bool
    {
        return $this->paidEvent;
    }

    public function setPaidEvent(?bool $paidEvent): static
    {
        $this->paidEvent = $paidEvent;
        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?string $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * @return Collection<int, Feature>
     */
    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function addFeature(Feature $feature): static
    {
        if (!$this->features->contains($feature)) {
            $this->features->add($feature);
        }

        return $this;
    }

    public function removeFeature(Feature $feature): static
    {
        $this->features->removeElement($feature);

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
