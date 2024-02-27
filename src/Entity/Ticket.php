<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['ticket:fRead']
            ]
        ),
        new GetCollection(
            uriTemplate: '/tickets',
            //controller: MyInvitationsController::class,
            openapiContext: [
                'summary' => 'get my ticket',
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
            description: 'get my ticket',
            name: 'myTicket',
        ),
        new Post(
            uriTemplate: '/tickets',
            //controller: AddInvitationController::class,
            openapiContext: [
                'summary' => 'add ticket',
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
            description: 'add ticket',
            name: 'addTicket',
        ),
        new Post(
            uriTemplate: '/ticket/check-code',
            //controller: CheckByCodeController::class,
            openapiContext: [
                'summary' => 'check ticket code',
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
            description: 'check ticket code',
            name: 'checkTicketCode',
        ),
        new Post(
            uriTemplate: '/tickets/{id}/send',
            //controller: SendInvitationController::class,
            openapiContext: [
                'summary' => 'send ticket',
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
            description: 'send ticket',
            name: 'sendTicket',
        ),
        new Post(
            uriTemplate: '/tickets/{id}/edit',
            //controller: EditInvitationController::class,
            openapiContext: [
                'summary' => 'edit ticket',
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
            description: 'edit ticket',
            name: 'editTicket',
        ),
        new Post(
            uriTemplate: '/tickets/send-all',
            //controller: SendAllController::class,
            openapiContext: [
                'summary' => 'send all tickets',
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
            description: 'send all tickets',
            name: 'sendAllTicket',
        ),
        new Put(),
        new Delete()
    ],
    order: ['name' => 'ASC']
)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?TicketType $ticketType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codePath = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    public function getTicketType(): ?TicketType
    {
        return $this->ticketType;
    }

    public function setTicketType(?TicketType $ticketType): static
    {
        $this->ticketType = $ticketType;

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
}
