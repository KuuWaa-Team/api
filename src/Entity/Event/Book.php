<?php

namespace App\Entity\Event;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Beverage\AddBeverageController;
use App\Controller\Book\DeleteController;
use App\Entity\Event;
use App\Entity\Invitation;
use App\Repository\Event\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/books/{id}/delete',
            controller: DeleteController::class,
            openapiContext: [
                'summary' => 'delete book',
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
            description: 'delete book',
            name: 'deleteBook',
        ),
        new Put(),
        new Delete()
    ],
    order: ['id' => 'ASC']
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Event $event = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[Groups(['event:pRead', 'event:fRead'])]
    private ?Invitation $invitation = null;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }

    public function setInvitation(?Invitation $invitation): static
    {
        $this->invitation = $invitation;

        return $this;
    }
}
