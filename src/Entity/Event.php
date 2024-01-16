<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Event\EditEventController;
use App\Controller\Event\EditEventImageController;
use App\Controller\Event\EventDetailController;
use App\Controller\Event\ListingAdminsController;
use App\Controller\Event\ListingEventBeveragesController;
use App\Entity\Event\EventAdmin;
use App\Entity\Payment\Payment;
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
            uriTemplate: "/events/{id}/admins",
            controller: ListingAdminsController::class,
            normalizationContext: [
                'groups' => ['read:event']
            ],
            name: "userEventAdmins"
        ),
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
            uriTemplate: "/events/{id}/beverages",
            controller: ListingEventBeveragesController::class,
            normalizationContext: [
                'groups' => ['read:event']
            ],
            name: "userTBeverages"
        ),
        new Get(
            uriTemplate: "/events/{id}",
            controller: EventDetailController::class,
            normalizationContext: [
                'groups' => ['event:fRead']
            ],
            name: "eventDetail"
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
        new Post(
            uriTemplate: '/events/{id}',
            controller: EditEventController::class,
            description: 'Edit event',
            name: 'editEvent',
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
        new Post(
            uriTemplate: '/events/{id}/image',
            inputFormats: ['multipart' => ['multipart/form-data']],
            controller: EditEventImageController::class,
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
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $details = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
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
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $imagePath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $tablesCount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $invitationsCount = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Beverage::class)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private Collection $beverages;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $beveragesCount = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventAdmin::class)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private Collection $eventAdmins;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $adminsCount = null;

    #[Groups(['event:pRead', 'event:fRead'])]
    private ?bool $shared = null;

    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $adminStatus = null;

    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $adminId = null;

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
        $this->invitationsCount = $this->invitations->count();
        $this->beverages = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->eventAdmins = new ArrayCollection();
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

    public function getInvitationsCount(): ?int
    {
        return $this->invitationsCount;
    }

    public function setInvitationsCount(?int $invitationsCount): static
    {
        $this->invitationsCount = $invitationsCount;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTablesCount(): ?int
    {
        return $this->tablesCount;
    }

    public function setTablesCount(?int $tablesCount): static
    {
        $this->tablesCount = $tablesCount;

        return $this;
    }

    /**
     * @return Collection<int, Beverage>
     */
    public function getBeverages(): Collection
    {
        return $this->beverages;
    }

    public function addBeverage(Beverage $beverage): static
    {
        if (!$this->beverages->contains($beverage)) {
            $this->beverages->add($beverage);
            $beverage->setEvent($this);
        }

        return $this;
    }

    public function removeBeverage(Beverage $beverage): static
    {
        if ($this->beverages->removeElement($beverage)) {
            // set the owning side to null (unless already changed)
            if ($beverage->getEvent() === $this) {
                $beverage->setEvent(null);
            }
        }

        return $this;
    }

    public function getBeveragesCount(): ?int
    {
        return $this->beveragesCount;
    }

    public function setBeveragesCount(?int $beveragesCount): static
    {
        $this->beveragesCount = $beveragesCount;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setEvent($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getEvent() === $this) {
                $payment->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventAdmin>
     */
    public function getEventAdmins(): Collection
    {
        return $this->eventAdmins;
    }

    public function addEventAdmin(EventAdmin $eventAdmin): static
    {
        if (!$this->eventAdmins->contains($eventAdmin)) {
            $this->eventAdmins->add($eventAdmin);
            $eventAdmin->setEvent($this);
        }

        return $this;
    }

    public function removeEventAdmin(EventAdmin $eventAdmin): static
    {
        if ($this->eventAdmins->removeElement($eventAdmin)) {
            // set the owning side to null (unless already changed)
            if ($eventAdmin->getEvent() === $this) {
                $eventAdmin->setEvent(null);
            }
        }

        return $this;
    }

    public function getAdminsCount(): ?int
    {
        return $this->adminsCount;
    }

    public function setAdminsCount(?int $adminsCount): static
    {
        $this->adminsCount = $adminsCount;

        return $this;
    }

    public function isShared(): ?bool
    {
        return $this->shared;
    }

    public function setShared(?bool $shared): static
    {
        $this->shared = $shared;

        return $this;
    }

    public function getAdminStatus(): ?string
    {
        return $this->adminStatus;
    }

    public function setAdminStatus(?string $adminStatus): static
    {
        $this->adminStatus = $adminStatus;

        return $this;
    }

    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    public function setAdminId(?int $adminId): static
    {
        $this->adminId = $adminId;

        return $this;
    }

}
