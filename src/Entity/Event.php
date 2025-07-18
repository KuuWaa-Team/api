<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Book\AddController;
use App\Controller\Event\EditEventController;
use App\Controller\Event\EditEventImageController;
use App\Controller\Event\EventDetailController;
use App\Controller\Event\Game\AddGameQuestionController;
use App\Controller\Event\Game\GameQuestionListController;
use App\Controller\Event\GenerateBookController;
use App\Controller\Event\ListingAdminsController;
use App\Controller\Event\ListingEventBeveragesController;
use App\Controller\Event\OpenBeverageController;
use App\Controller\Event\OpenBookController;
use App\Controller\Event\OpenTablesController;
use App\Entity\EventTemplate\TemplateRequirementValue;
use App\Entity\Event\Book;
use App\Entity\Event\EventAdmin;
use App\Entity\Event\GameQuestion;
use App\Entity\Event\Gift;
use App\Entity\Event\EventSpecification;
use App\Entity\Event\EventTemplate;
use ApiPlatform\Metadata\Put;
use App\Controller\Billing\EventPaymentProcessController;
use App\Controller\Billing\EventPaymentStatusController;
use App\Controller\Event\ActivePackEventController;
use App\Controller\Event\AddInvitationEventController;
use App\Controller\Event\AddTableEventController;
use App\Controller\Event\CreateEventController;
use App\Controller\Event\DeleteEventController;
use App\Controller\Event\ListingEventInvitationsController;
use App\Controller\Event\ListingEventTablesController;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
            uriTemplate: '/events/{id}/book',
            controller: AddController::class,
            openapiContext: [
                'summary' => 'add book',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                            ]
                        ]
                    ]
                ],
            ],
            description: 'Active book',
            name: 'addEventBook',
        ),
        new Post(
            uriTemplate: '/events/{id}/open-tables',
            controller: OpenTablesController::class,
            openapiContext: [
                'summary' => 'Allow table management',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                            ]
                        ]
                    ]
                ],
            ],
            description: 'open tables',
            name: 'openEventTables',
        ),
        new Post(
            uriTemplate: '/events/{id}/open-beverage',
            controller: OpenBeverageController::class,
            openapiContext: [
                'summary' => 'Allow beverage management',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                            ]
                        ]
                    ]
                ],
            ],
            description: 'open beverage',
            name: 'openEventBeverage',
        ),
        new Post(
            uriTemplate: '/events/{id}/open-book',
            controller: OpenBookController::class,
            openapiContext: [
                'summary' => 'Allow book management',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                            ]
                        ]
                    ]
                ],
            ],
            description: 'open book',
            name: 'openEventBook',
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
            uriTemplate: '/events/{id}/pay-pack',
            controller: EventPaymentProcessController::class,
            openapiContext: [
                'summary' => 'Add payment pack for event',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'phoneNumber' => [
                                        'type' => 'string',
                                        'example' => '243XXXXXXXXX',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            description: 'Add invitation for event',
            name: 'addPay',
        ),
        new Get(
            uriTemplate: "/events/{id}/check-pay",
            controller: EventPaymentStatusController::class,
            normalizationContext: [
                'groups' => ['event:fRead']
            ],
            name: "getEventPayStatus"
        ),
        new Post(
            uriTemplate: '/events/{id}',
            controller: EditEventController::class,
            description: 'Edit event',
            name: 'editEvent',
        ),
        new Post(
            uriTemplate: '/events/{id}/delete',
            controller: DeleteEventController::class,
            description: 'Delete event',
            name: 'deleteEvent',
        ),
        new Get(
            uriTemplate: '/events/{id}/generate-book',
            controller: GenerateBookController::class,
            description: 'Generate book',
            name: 'generateBook',
        ),
        new Get(
            uriTemplate: '/events/{id}/questions-list',
            controller: GameQuestionListController::class,
            description: 'Game Questions List',
            name: 'gameQuestionsList',
        ),
        new Post(
            uriTemplate: '/events/{id}/add-game-question',
            controller: AddGameQuestionController::class,
            description: 'Add game question',
            deserialize: false,
            name: 'addGameQuestion'
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
        new Post(
            uriTemplate: '/events/{id}/import-invitations',
            inputFormats: ['multipart' => ['multipart/form-data']],
            deserialize: false
        ),

        new Put(),
        new Delete(
            controller: DeleteEventController::class
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
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?EventCategory $category = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Invitation::class, cascade: ['remove'])]
    private Collection $invitations;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Table::class, cascade: ['remove'])]
    private Collection $tables;
    
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $imagePath = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $tablesCount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $invitationsCount = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Beverage::class, cascade: ['remove'])]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private Collection $beverages;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?int $beveragesCount = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventAdmin::class, cascade: ['remove'])]
    #[Groups(['event:pRead', 'event:fRead'])]
    private Collection $eventAdmins;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $adminsCount = null;

    #[Groups(['event:pRead', 'event:fRead'])]
    private ?bool $shared = null;

    #[Groups(['event:pRead', 'event:fRead'])]
    private ?bool $canSendInvitations = null;

    /**
     * @return bool|null
     */
    public function getCanSendInvitations(): ?bool
    {
        return $this->getUser()->getCredits() >= $this->getTemplate()->getPrice();
    }

    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $adminStatus = null;

    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $adminRoles = null;

    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $adminId = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Book::class, cascade: ['remove'])]
    #[Groups(['event:pRead', 'event:fRead'])]
    private Collection $books;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $thumbPath = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?User $owner = null;

    /**
     * @var Collection<int, EventSpecification>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventSpecification::class, cascade: ['persist', 'remove'])]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private Collection $eventSpecifications;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $placeName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $address = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?EventTemplate $template = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $details = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?bool $hasTables = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?bool $hasBeverage = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?bool $hasBook = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $placeLongitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $placeLatitude = null;

    /**
     * @var Collection<int, Gift>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Gift::class)]
    private Collection $gifts;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $templateImage = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?bool $hasQuiz = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?bool $hasGift = null;

    /**
     * @var Collection<int, GameQuestion>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: GameQuestion::class)]
    private Collection $questions;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?bool $hasMusic = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?bool $hasGame = null;

    /**
     * @var Collection<int, TemplateRequirementValue>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: TemplateRequirementValue::class)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private Collection $templateRequirementValues;

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
        $this->invitationsCount = $this->invitations->count();
        $this->beverages = new ArrayCollection();
        $this->eventAdmins = new ArrayCollection();
        $this->books = new ArrayCollection();
        $this->gifts = new ArrayCollection();
        $this->eventSpecifications = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->templateRequirementValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getInvitationsCount(): ?int
    {
        return $this->invitationsCount;
    }

    public function setInvitationsCount(?int $invitationsCount): static
    {
        $this->invitationsCount = $invitationsCount;

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


    public function setAdminRoles(?string $adminRoles): static
    {
        $this->adminRoles = $adminRoles;

        return $this;
    }

    public function getAdminRoles(): ?string
    {
        return $this->adminRoles;
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

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setEvent($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getEvent() === $this) {
                $book->setEvent(null);
            }
        }

        return $this;
    }

    public function getThumbPath(): ?string
    {
        return $this->thumbPath;
    }

    public function setThumbPath(?string $thumbPath): static
    {
        $this->thumbPath = $thumbPath;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, EventSpecification>
     */
    public function getEventSpecifications(): Collection
    {
        return $this->eventSpecifications;
    }

    public function addEventSpecification(EventSpecification $eventSpecification): static
    {
        if (!$this->eventSpecifications->contains($eventSpecification)) {
            $this->eventSpecifications->add($eventSpecification);
            $eventSpecification->setEvent($this);
        }

        return $this;
    }

    public function removeEventSpecification(EventSpecification $eventSpecification): static
    {
        if ($this->eventSpecifications->removeElement($eventSpecification)) {
            // set the owning side to null (unless already changed)
            if ($eventSpecification->getEvent() === $this) {
                $eventSpecification->setEvent(null);
            }
        }

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getPlaceName(): ?string
    {
        return $this->placeName;
    }

    public function setPlaceName(?string $placeName): static
    {
        $this->placeName = $placeName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getTemplate(): ?EventTemplate
    {
        return $this->template;
    }

    public function setTemplate(?EventTemplate $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getHasTables(): ?bool
    {
        return $this->hasTables;
    }

    public function getHasBeverage(): ?bool
    {
        return $this->hasBeverage;
    }

    public function getHasBook(): ?bool
    {
        return $this->hasBook;
    }

    public function hetasTables(): ?bool
    {
        return $this->hasTables;
    }

    public function setHasTables(?bool $hasTables): static
    {
        $this->hasTables = $hasTables;

        return $this;
    }

    public function hasBeverage(): ?bool
    {
        return $this->hasBeverage;
    }

    public function setHasBeverage(?bool $hasBeverage): static
    {
        $this->hasBeverage = $hasBeverage;

        return $this;
    }

    public function hasBook(): ?bool
    {
        return $this->hasBook;
    }

    public function setHasBook(?bool $hasBook): static
    {
        $this->hasBook = $hasBook;

        return $this;
    }

    public function getPlaceLongitude(): ?string
    {
        return $this->placeLongitude;
    }

    public function setPlaceLongitude(?string $placeLongitude): static
    {
        $this->placeLongitude = $placeLongitude;

        return $this;
    }

    public function getPlaceLatitude(): ?string
    {
        return $this->placeLatitude;
    }

    public function setPlaceLatitude(?string $placeLatitude): static
    {
        $this->placeLatitude = $placeLatitude;

        return $this;
    }

    /**
     * @return Collection<int, Gift>
     */
    public function getGifts(): Collection
    {
        return $this->gifts;
    }

    public function addGift(Gift $gift): static
    {
        if (!$this->gifts->contains($gift)) {
            $this->gifts->add($gift);
            $gift->setEvent($this);
        }

        return $this;
    }

    public function removeGift(Gift $gift): static
    {
        if ($this->gifts->removeElement($gift)) {
            // set the owning side to null (unless already changed)
            if ($gift->getEvent() === $this) {
                $gift->setEvent(null);
            }
        }

        return $this;
    }

    public function getTemplateImage(): ?string
    {
        return $this->templateImage;
    }

    public function setTemplateImage(?string $templateImage): static
    {
        $this->templateImage = $templateImage;

        return $this;
    }

    public function hasQuiz(): ?bool
    {
        return $this->hasQuiz;
    }

    public function setHasQuiz(?bool $hasQuiz): static
    {
        $this->hasQuiz = $hasQuiz;

        return $this;
    }

    public function hasGift(): ?bool
    {
        return $this->hasGift;
    }

    public function setHasGift(?bool $hasGift): static
    {
        $this->hasGift = $hasGift;

        return $this;
    }

    /**
     * @return Collection<int, GameQuestion>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(GameQuestion $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setEvent($this);
        }

        return $this;
    }

    public function removeQuestion(GameQuestion $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getEvent() === $this) {
                $question->setEvent(null);
            }
        }

        return $this;
    }

    public function hasMusic(): ?bool
    {
        return $this->hasMusic;
    }

    public function setHasMusic(?bool $hasMusic): static
    {
        $this->hasMusic = $hasMusic;

        return $this;
    }

    public function hasGame(): ?bool
    {
        return $this->hasGame;
    }

    public function setHasGame(?bool $hasGame): static
    {
        $this->hasGame = $hasGame;

        return $this;
    }

    /**
     * @return Collection<int, TemplateRequirementValue>
     */
    public function getTemplateRequirementValues(): Collection
    {
        return $this->templateRequirementValues;
    }

    public function addTemplateRequirementValue(TemplateRequirementValue $templateRequirementValue): static
    {
        if (!$this->templateRequirementValues->contains($templateRequirementValue)) {
            $this->templateRequirementValues->add($templateRequirementValue);
            $templateRequirementValue->setEvent($this);
        }

        return $this;
    }

    public function removeTemplateRequirementValue(TemplateRequirementValue $templateRequirementValue): static
    {
        if ($this->templateRequirementValues->removeElement($templateRequirementValue)) {
            // set the owning side to null (unless already changed)
            if ($templateRequirementValue->getEvent() === $this) {
                $templateRequirementValue->setEvent(null);
            }
        }

        return $this;
    }
}
