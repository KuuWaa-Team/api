<?php

namespace App\Entity;

use App\Repository\EventPackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventPackRepository::class)]
class EventPack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $maxTickets = null;

    // TABLE_MANAGEMENT
    // REPORT_MANAGEMENT
    // BEVERAGE_MANAGEMENT
    #[ORM\Column]
    private array $features = [];

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
}
