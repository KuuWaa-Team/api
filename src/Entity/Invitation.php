<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Beverage\SelectBeverageController;
use App\Controller\Event\ActivePackEventController;
use App\Controller\Event\CreateEventController;
use App\Controller\Invitation\AddInvitationController;
use App\Controller\Invitation\CheckByCodeController;
use App\Controller\Invitation\EditInvitationController;
use App\Controller\Invitation\MyInvitationsController;
use App\Controller\Invitation\PresenceConfirmationController;
use App\Controller\Invitation\SendAllController;
use App\Controller\Invitation\SendInvitationController;
use App\Controller\Invitation\ValidateController;
use App\Entity\Event\Book;
use App\Entity\Event\Gift;
use App\Entity\Question\Answer;
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
            uriTemplate: '/invitations/{id}/beverage',
            controller: SelectBeverageController::class,
            openapiContext: [
                'summary' => 'Select beverage',
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
            description: 'Select beverage',
            name: 'selectInvitationBeverage',
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

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    #[Groups(["invitation:pRead", 'invitation:fRead'])]
    private ?Beverage $beverage = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["invitation:pRead", 'invitation:fRead'])]
    private ?string $codePath = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["invitation:pRead", 'invitation:fRead'])]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'invitation', targetEntity: Book::class)]
    private Collection $books;

    #[ORM\Column(nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead', 'event:pRead', 'event:fRead'])]
    private ?bool $bookSent = null;

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

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(mappedBy: 'invitation', targetEntity: Answer::class)]
    private Collection $answers;

    #[ORM\Column(nullable: true)]
    private ?bool $willBePresent = null;

    /**
     * @var Collection<int, Gift>
     */
    #[ORM\ManyToMany(targetEntity: Gift::class, inversedBy: 'invitations')]
    private Collection $gifts;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->invitations = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->gifts = new ArrayCollection();
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

    public function getBeverage(): ?Beverage
    {
        return $this->beverage;
    }

    public function setBeverage(?Beverage $beverage): static
    {
        $this->beverage = $beverage;

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
            $book->setInvitation($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getInvitation() === $this) {
                $book->setInvitation(null);
            }
        }

        return $this;
    }

    public function isBookSent(): ?bool
    {
        return $this->bookSent;
    }

    public function setBookSent(?bool $bookSent): static
    {
        $this->bookSent = $bookSent;

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

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setInvitation($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getInvitation() === $this) {
                $answer->setInvitation(null);
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

}
