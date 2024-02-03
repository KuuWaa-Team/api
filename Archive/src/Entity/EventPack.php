<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\EventCategory\GetEventCategoriesController;
use App\Controller\EventPack\GetEventPackController;
use App\Repository\EventPackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: "/event_packs",
            controller: GetEventPackController::class,
            name: "eventPacks",
        ),
        new Get(),
        new Post(),
        new Put(),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: EventPackRepository::class)]
class EventPack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["pack:pRead", "event:pRead", "event:fRead"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["pack:pRead", "event:pRead", "event:fRead"])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    #[Groups(["pack:pRead", "event:pRead", "event:fRead"])]
    private ?string $price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["pack:pRead", "event:pRead", "event:fRead"])]
    private ?int $maxTickets = null;

    #[ORM\OneToMany(mappedBy: 'eventPack', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\Column(nullable: true)]
    private ?bool $paid = null;

    #[ORM\Column(nullable: true)]
    private ?int $packOrder = null;

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

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getMaxTickets(): ?int
    {
        return $this->maxTickets;
    }

    public function setMaxTickets(?int $maxTickets): static
    {
        $this->maxTickets = $maxTickets;

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
            $event->setEventPack($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getEventPack() === $this) {
                $event->setEventPack(null);
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

    public function addFeature(Feature $feature): static
    {
        if (!$this->features->contains($feature)) {
            $this->features->add($feature);
            $feature->setPack($this);
        }

        return $this;
    }

    public function removeFeature(Feature $feature): static
    {
        if ($this->features->removeElement($feature)) {
            // set the owning side to null (unless already changed)
            if ($feature->getPack() === $this) {
                $feature->setPack(null);
            }
        }

        return $this;
    }

    public function getPackOrder(): ?int
    {
        return $this->packOrder;
    }

    public function setPackOrder(?int $packOrder): static
    {
        $this->packOrder = $packOrder;

        return $this;
    }
}
