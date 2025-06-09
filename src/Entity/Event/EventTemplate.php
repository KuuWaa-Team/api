<?php

namespace App\Entity\Event;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Template\GetAllController;
use App\Entity\Event;
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
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?string $code = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?string $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?string $imagePath = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?bool $hasAnimations = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?string $eventsAdapted = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(mappedBy: 'template', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\Column(nullable: true)]
    private ?bool $imageRequired = null;

    #[ORM\Column(length: 255)]
    private ?string $primaryColor = null;

    #[ORM\Column(length: 255)]
    private ?string $secondaryColor = null;

    public function __construct()
    {
        $this->events = new ArrayCollection();
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

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function hasAnimations(): ?bool
    {
        return $this->hasAnimations;
    }

    public function setHasAnimations(?bool $hasAnimations): static
    {
        $this->hasAnimations = $hasAnimations;

        return $this;
    }

    public function getEventsAdapted(): ?string
    {
        return $this->eventsAdapted;
    }

    public function setEventsAdapted(?string $eventsAdapted): static
    {
        $this->eventsAdapted = $eventsAdapted;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setTemplate($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getTemplate() === $this) {
                $event->setTemplate(null);
            }
        }

        return $this;
    }

    public function isImageRequired(): ?bool
    {
        return $this->imageRequired;
    }

    public function setImageRequired(?bool $imageRequired): static
    {
        $this->imageRequired = $imageRequired;

        return $this;
    }

    public function getPrimaryColor(): ?string
    {
        return $this->primaryColor;
    }

    public function setPrimaryColor(string $primaryColor): static
    {
        $this->primaryColor = $primaryColor;

        return $this;
    }

    public function getSecondaryColor(): ?string
    {
        return $this->secondaryColor;
    }

    public function setSecondaryColor(string $secondaryColor): static
    {
        $this->secondaryColor = $secondaryColor;

        return $this;
    }
}
