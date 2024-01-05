<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EventPackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[ORM\Entity(repositoryClass: EventPackRepository::class)]
class EventPack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["read:event"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["read:event"])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    #[Groups(["read:event"])]
    private ?string $price = null;

    #[ORM\Column]
    #[Groups(["read:event"])]
    private ?int $maxTickets = null;

    // TABLE_MANAGEMENT
    // REPORT_MANAGEMENT
    // BEVERAGE_MANAGEMENT
    #[ORM\Column]
    #[Groups(["read:event"])]
    private array $features = [];

    #[ORM\OneToMany(mappedBy: 'eventPack', targetEntity: Event::class)]
    private Collection $events;

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

    public function setMaxTickets(int $maxTickets): static
    {
        $this->maxTickets = $maxTickets;

        return $this;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function setFeatures(array $features): static
    {
        $this->features = $features;

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
}
