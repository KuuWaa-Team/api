<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Event\ActivePackEventController;
use App\Controller\Event\AddInvitationEventController;
use App\Controller\Event\AddTableEventController;
use App\Controller\Event\CreateEventController;
use App\Controller\Event\ListingEventInvitationsController;
use App\Controller\Event\ListingEventTablesController;
use App\Controller\Event\ListingUserEventController;
use App\Controller\EventCategory\GetEventCategoriesController;
use App\Entity\Event\EventTemplate;
use App\Repository\EventCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: "/event_categories",
            controller: GetEventCategoriesController::class,
            name: "eventCategories",
        ),
        new Get(),
        new Post(),
        new Put(),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: EventCategoryRepository::class)]
class EventCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:pRead', 'event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category:pRead', 'event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\Column(nullable: true)]
    private ?bool $paid = null;

    #[ORM\Column(nullable: true)]
    private ?bool $validated = null;

    /**
     * @var Collection<int, EventTemplate>
     */
    #[ORM\ManyToMany(targetEntity: EventTemplate::class, mappedBy: 'eventCategory')]
    private Collection $eventTemplates;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->eventTemplates = new ArrayCollection();
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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
            $event->setCategory($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCategory() === $this) {
                $event->setCategory(null);
            }
        }

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(?bool $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(?bool $validated): static
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * @return Collection<int, EventTemplate>
     */
    public function getEventTemplates(): Collection
    {
        return $this->eventTemplates;
    }

    public function addEventTemplate(EventTemplate $eventTemplate): static
    {
        if (!$this->eventTemplates->contains($eventTemplate)) {
            $this->eventTemplates->add($eventTemplate);
            $eventTemplate->addEventCategory($this);
        }

        return $this;
    }

    public function removeEventTemplate(EventTemplate $eventTemplate): static
    {
        if ($this->eventTemplates->removeElement($eventTemplate)) {
            $eventTemplate->removeEventCategory($this);
        }

        return $this;
    }
}
