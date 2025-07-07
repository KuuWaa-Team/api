<?php

namespace App\Entity\Event;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Beverage\AddBeverageController;
use App\Controller\EventAdmin\AcceptInvitationController;
use App\Controller\EventAdmin\AddAdminController;
use App\Controller\EventAdmin\RefuseInvitationController;
use App\Entity\Event;
use App\Repository\Event\EventAdminRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EventAdminRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/event-admins',
            controller: AddAdminController::class,
            openapiContext: [
                'summary' => 'add admin',
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
            description: 'add admin',
            name: 'addAdmin',
        ),
        new Post(
            uriTemplate: '/event-admins/{id}/accept',
            controller: AcceptInvitationController::class,
            openapiContext: [
                'summary' => 'accept invitation',
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
            description: 'accept invitation',
            name: 'acceptInvitation',
        ),
        new Post(
            uriTemplate: '/event-admins/{id}/refuse',
            controller: RefuseInvitationController::class,
            openapiContext: [
                'summary' => 'refuse invitation',
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
            description: 'refuse invitation',
            name: 'refuseInvitation',
        ),
        new Put(),
        new Delete()
    ],
    order: ['name' => 'ASC']
)]
class EventAdmin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:pRead', 'event:fRead', 'admin:pRead'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'eventAdmins')]
    private ?Event $event = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'admin:pRead'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'admin:pRead'])]
    private ?string $status = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $roles = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    public function getRoles(): ?string
    {
        return $this->roles;
    }

    public function setRoles(?string $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
}
