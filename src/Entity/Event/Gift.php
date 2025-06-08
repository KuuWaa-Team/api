<?php

namespace App\Entity\Event;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Gift\AddController;
use App\Controller\Gift\DeleteController;
use App\Controller\Gift\SelectController;
use App\Entity\Event;
use App\Entity\Invitation;
use App\Repository\Event\GiftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GiftRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/gift/{id}/add',
            controller: AddController::class,
            openapiContext: [
                'summary' => 'Add gift',
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
            description: 'add gift',
            name: 'addGift',
        ),
        new Post(
            uriTemplate: '/gift/{id}/delete',
            controller: DeleteController::class,
            openapiContext: [
                'summary' => 'delete gift',
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
            description: 'delete gift',
            name: 'deleteGift',
        ),
        new Post(
            uriTemplate: '/gift/{id}/select',
            controller: SelectController::class,
            openapiContext: [
                'summary' => 'select gift',
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
            description: 'select gift',
            name: 'selectGift',
        ),
    ],
    order: ['name' => 'ASC']
)]
class Gift
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:fRead', 'invitation:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:fRead', 'invitation:fRead'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Invitation>
     */
    #[ORM\ManyToMany(targetEntity: Invitation::class, mappedBy: 'gifts')]
    private Collection $invitations;

    #[ORM\ManyToOne(inversedBy: 'gifts')]
    private ?Event $event = null;

    public function __construct()
    {
        $this->invitations = new ArrayCollection();
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
            $invitation->addGift($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            $invitation->removeGift($this);
        }

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
}
