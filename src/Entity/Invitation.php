<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Event\ActivePackEventController;
use App\Controller\Event\CreateEventController;
use App\Controller\Invitation\AddInvitationController;
use App\Controller\Invitation\SendInvitationController;
use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
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
        new Put(),
        new Delete()
    ],
    order: ['name' => 'ASC']
)]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["invitation:pRead", 'table:pRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["invitation:pRead", 'table:pRead'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["invitation:pRead", 'table:pRead'])]
    private ?string $civility = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead'])]
    private ?int $count = null;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    private ?Event $event = null;

    #[ORM\Column(length: 255)]
    #[Groups(["invitation:pRead", 'table:pRead'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["invitation:pRead", 'table:pRead'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    #[Groups(["invitation:pRead"])]
    private ?Table $invitationTable = null;

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

    public function setPhoneNumber(string $phoneNumber): static
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

}
