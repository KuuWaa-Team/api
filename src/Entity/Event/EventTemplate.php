<?php

namespace App\Entity\Event;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Template\GetAllController;
use App\Entity\EventCategory;
use App\Repository\EventTemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: "/event_templates",
            controller: GetAllController::class,
            name: "eventTemplates",
        ),
        new Get(),
        new Post(),
        new Put(),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: EventTemplateRepository::class)]
class EventTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['templates:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['templates:fRead'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['templates:fRead'])]
    private ?string $code = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    #[Groups(['templates:fRead'])]
    private ?string $price = null;

    /**
     * @var Collection<int, EventCategory>
     */
    #[ORM\ManyToMany(targetEntity: EventCategory::class, inversedBy: 'eventTemplates')]
    private Collection $eventCategory;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['templates:fRead'])]
    private ?string $imagePath = null;

    public function __construct()
    {
        $this->eventCategory = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, EventCategory>
     */
    public function getEventCategory(): Collection
    {
        return $this->eventCategory;
    }

    public function addEventCategory(EventCategory $eventCategory): static
    {
        if (!$this->eventCategory->contains($eventCategory)) {
            $this->eventCategory->add($eventCategory);
        }

        return $this;
    }

    public function removeEventCategory(EventCategory $eventCategory): static
    {
        $this->eventCategory->removeElement($eventCategory);

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }
}
