<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Invitation\AddInvitationController;
use App\Controller\Invitation\CheckByCodeController;
use App\Controller\Invitation\EditInvitationController;
use App\Controller\Invitation\MyInvitationsController;
use App\Controller\Invitation\PresenceConfirmationController;
use App\Controller\Invitation\SendAllController;
use App\Controller\Invitation\SendInvitationController;
use App\Controller\Invitation\ValidateController;
use App\Entity\Event\Book;
use App\Entity\Event\GameResponse;
use App\Entity\Event\Gift;
use App\Entity\Event\Photo;
use App\Repository\InvitationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['invitation:fRead']
            ]
        ),
        new GetCollection(
            uriTemplate: '/invitations',
            controller: MyInvitationsController::class,
            openapiContext: [
                'summary' => 'get my invitation',
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
            description: 'get my invitation',
            name: 'myInvitation',
        ),
        new Post(
            uriTemplate: '/invitations',
            controller: AddInvitationController::class,
            openapiContext: [
                'summary' => 'add invitation',
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
            description: 'add invitation',
            name: 'addInvitation',
        ),
        new Post(
            uriTemplate: '/invitations/check-code',
            controller: CheckByCodeController::class,
            openapiContext: [
                'summary' => 'check invitation code',
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
            description: 'check invitation code',
            name: 'checkInvitationCode',
        ),
        new Post(
            uriTemplate: '/invitations/{id}/send',
            controller: SendInvitationController::class,
            openapiContext: [
                'summary' => 'send invitation',
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
            description: 'send invitation',
            name: 'sendInvitation',
        ),
        new Post(
            uriTemplate: '/invitations/{id}/edit',
            controller: EditInvitationController::class,
            openapiContext: [
                'summary' => 'edit invitation',
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
            description: 'edit invitation',
            name: 'editInvitation',
        ),
        new Post(
            uriTemplate: '/invitations/send-all',
            controller: SendAllController::class,
            openapiContext: [
                'summary' => 'send all invitations',
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
            description: 'send all invitation',
            name: 'sendAllInvitation',
        ),
        new Post(
            uriTemplate: '/invitations/{id}/validate',
            controller: ValidateController::class,
            openapiContext: [
                'summary' => 'validate invitation',
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
            description: 'Validate invitation',
            name: 'validateInvitation',
        ),
        new Post(
            uriTemplate: '/invitations/{id}/presence',
            controller: PresenceConfirmationController::class,
            openapiContext: [
                'summary' => 'Set presence status',
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
            description: 'Set Presence Status',
            name: 'setPresenceStatus',
        ),
        new Put(),
        new Delete()
    ],
    order: ['name' => 'ASC']
)]
class Invitation
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead', 'event:pRead', 'event:fRead'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead', 'event:pRead', 'event:fRead'])]
    private ?string $civility = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead'])]
    private ?int $count = null;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    #[Groups(['invitation:fRead'])]
    private ?Event $event = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead'])]
    private ?Table $invitationTable = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["invitation:pRead", 'invitation:fRead'])]
    private ?string $codePath = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["invitation:pRead", 'invitation:fRead'])]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead', 'event:pRead', 'event:fRead'])]
    private ?string $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead'])]
    private ?string $decryptKey = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'invitations')]
    private ?self $eventTemplate = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(mappedBy: 'eventTemplate', targetEntity: self::class)]
    private Collection $invitations;

    #[ORM\Column(nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead', 'event:pRead', 'event:fRead'])]
    private ?bool $willBePresent = null;

    /**
     * @var Collection<int, Gift>
     */
    #[ORM\ManyToMany(targetEntity: Gift::class, inversedBy: 'invitations')]
    private Collection $gifts;

    /**
     * @var Collection<int, Beverage>
     */
    #[ORM\ManyToMany(targetEntity: Beverage::class, inversedBy: 'invitations')]
    private Collection $beverages;

    #[ORM\OneToOne(inversedBy: 'invitation', cascade: ['persist', 'remove'])]
    private ?Book $book = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead', 'event:pRead', 'event:fRead'])]
    private ?string $musicChoice = null;

    /**
     * @var Collection<int, Photo>
     */
    #[ORM\OneToMany(mappedBy: 'invitation', targetEntity: Photo::class)]
    private Collection $photos;

    #[ORM\Column(nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead', 'event:pRead', 'event:fRead'])]
    private ?bool $musicPlayed = null;

    /**
     * @var Collection<int, GameResponse>
     */
    #[ORM\OneToMany(mappedBy: 'invitation', targetEntity: GameResponse::class)]
    private Collection $gameResponses;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gameStatus = null;

    public function __construct()
    {
        $this->invitations = new ArrayCollection();
        $this->gifts = new ArrayCollection();
        $this->beverages = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->gameResponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isFemale(): bool
    {
        return $this->civility === 'Mlle' || $this->civility === 'Mme';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getFullname(): ?string
    {
        return $this->civility . " " . $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(string $civility): static
    {
        $this->civility = $civility;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

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

    public function getInvitationTable(): ?Table
    {
        return $this->invitationTable;
    }

    public function setInvitationTable(?Table $invitationTable): static
    {
        $this->invitationTable = $invitationTable;

        return $this;
    }
    public function getCodePath(): ?string
    {
        return $this->codePath;
    }

    public function setCodePath(?string $codePath): static
    {
        $this->codePath = $codePath;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getDecryptKey(): ?string
    {
        return $this->decryptKey;
    }

    public function setDecryptKey(?string $decryptKey): static
    {
        $this->decryptKey = $decryptKey;

        return $this;
    }

    public function getEventTemplate(): ?self
    {
        return $this->eventTemplate;
    }

    public function setEventTemplate(?self $eventTemplate): static
    {
        $this->eventTemplate = $eventTemplate;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(self $invitation): static
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setEventTemplate($this);
        }

        return $this;
    }

    public function removeInvitation(self $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getEventTemplate() === $this) {
                $invitation->setEventTemplate(null);
            }
        }

        return $this;
    }
    public function isWillBePresent(): ?bool
    {
        return $this->willBePresent;
    }

    public function setWillBePresent(?bool $willBePresent): static
    {
        $this->willBePresent = $willBePresent;

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
        }

        return $this;
    }

    public function removeGift(Gift $gift): static
    {
        $this->gifts->removeElement($gift);

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
        }

        return $this;
    }

    public function removeBeverage(Beverage $beverage): static
    {
        $this->beverages->removeElement($beverage);

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getMusicChoice(): ?string
    {
        return $this->musicChoice;
    }

    public function setMusicChoice(?string $musicChoice): static
    {
        $this->musicChoice = $musicChoice;

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setInvitation($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getInvitation() === $this) {
                $photo->setInvitation(null);
            }
        }

        return $this;
    }

    public function isMusicPlayed(): ?bool
    {
        return $this->musicPlayed;
    }

    public function setMusicPlayed(?bool $musicPlayed): static
    {
        $this->musicPlayed = $musicPlayed;

        return $this;
    }

    /**
     * @return Collection<int, GameResponse>
     */
    public function getGameResponses(): Collection
    {
        return $this->gameResponses;
    }

    public function addGameResponse(GameResponse $gameResponse): static
    {
        if (!$this->gameResponses->contains($gameResponse)) {
            $this->gameResponses->add($gameResponse);
            $gameResponse->setInvitation($this);
        }

        return $this;
    }

    public function removeGameResponse(GameResponse $gameResponse): static
    {
        if ($this->gameResponses->removeElement($gameResponse)) {
            // set the owning side to null (unless already changed)
            if ($gameResponse->getInvitation() === $this) {
                $gameResponse->setInvitation(null);
            }
        }

        return $this;
    }

    public function getGameStatus(): ?string
    {
        return $this->gameStatus;
    }

    public function setGameStatus(?string $gameStatus): static
    {
        $this->gameStatus = $gameStatus;

        return $this;
    }

}
