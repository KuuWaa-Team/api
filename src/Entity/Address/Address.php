<?php

namespace App\Entity\Address;

use App\Repository\Address\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $street = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $number = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?Province $province = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?Country $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $ref = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setProvince(?Province $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }
}
