<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Beverage\AddBeverageController;
use App\Controller\Table\AddTableController;
use App\Repository\BeverageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BeverageRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/beverages',
            controller: AddBeverageController::class,
            openapiContext: [
                'summary' => 'add beverage',
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
            description: 'add beverage',
            name: 'addBeverage',
        ),
        new Put(),
        new Delete()
    ],
    order: ['name' => 'ASC']
)]
class Beverage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["beverage:pRead", 'event:pRead', 'event:fRead', 'invitation:fRead', "invitation:pRead"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["beverage:pRead", 'event:pRead', 'event:fRead', 'invitation:fRead', "invitation:pRead"])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["beverage:pRead", 'invitation:fRead', 'event:fRead'])]
    private ?int $countTaken = null;

    #[ORM\ManyToOne(inversedBy: 'beverages')]
    private ?Event $event = null;

    #[ORM\Column(length: 255)]
    #[Groups(["beverage:pRead", 'invitation:fRead', 'event:fRead'])]
    private ?string $type = null;

    /**
     * @var Collection<int, Invitation>
     */
    #[ORM\ManyToMany(targetEntity: Invitation::class, mappedBy: 'beverages')]
    private Collection $invitations;

    private ?string $countTakenText = null;

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

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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
            $invitation->addBeverage($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            $invitation->removeBeverage($this);
        }

        return $this;
    }

    public function getCountTakenText(): ?string
    {
        return $this->countTakenText;
    }

    public function setCountTakenText(string $countTakenText): static
    {
        $this->countTakenText = $countTakenText;

        return $this;
    }
}
