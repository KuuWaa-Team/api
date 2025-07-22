<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Table\AddTableController;
use App\Controller\Table\EditTableController;
use App\Repository\TableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TableRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/tables',
            controller: AddTableController::class,
            openapiContext: [
                'summary' => 'add table',
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
            description: 'add table',
            name: 'addTabble',
        ),
        new Post(
            uriTemplate: '/tables/{id}/edit',
            controller: EditTableController::class,
            openapiContext: [
                'summary' => 'edit table',
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
            description: 'edit table',
            name: 'editTable',
        ),
        new Put(),
        new Delete()
    ],
    order: ['name' => 'ASC']
)]
#[ORM\Table(name: '`table`')]
class Table
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(["invitation:pRead", 'table:pRead', 'invitation:fRead'])]
    private ?int $count = null;

    #[ORM\ManyToOne(inversedBy: 'tables')]
    private ?Event $event = null;

    #[ORM\OneToMany(mappedBy: 'invitationTable', targetEntity: Invitation::class)]
    #[Groups(['table:pRead'])]
    private Collection $invitations;

    #[ORM\Column(nullable: true)]
    #[Groups(['table:pRead'])]
    private ?int $countTaken = null;

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

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
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
            $invitation->setInvitationTable($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getInvitationTable() === $this) {
                $invitation->setInvitationTable(null);
            }
        }

        return $this;
    }

    public function getCountTaken(): ?int
    {
        return $this->countTaken;
    }

    public function setCountTaken(?int $countTaken): static
    {
        $this->countTaken = $countTaken;

        return $this;
    }
}
