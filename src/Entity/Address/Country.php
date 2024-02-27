<?php

namespace App\Entity\Address;

use App\Repository\Address\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneInitial = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shortName = null;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: Address::class)]
    private Collection $addresses;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: Province::class)]
    private Collection $provinces;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->provinces = new ArrayCollection();
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

    public function getPhoneInitial(): ?string
    {
        return $this->phoneInitial;
    }

    public function setPhoneInitial(?string $phoneInitial): static
    {
        $this->phoneInitial = $phoneInitial;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): static
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setCountry($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getCountry() === $this) {
                $address->setCountry(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Province>
     */
    public function getProvinces(): Collection
    {
        return $this->provinces;
    }

    public function addProvince(Province $province): static
    {
        if (!$this->provinces->contains($province)) {
            $this->provinces->add($province);
            $province->setCountry($this);
        }

        return $this;
    }

    public function removeProvince(Province $province): static
    {
        if ($this->provinces->removeElement($province)) {
            // set the owning side to null (unless already changed)
            if ($province->getCountry() === $this) {
                $province->setCountry(null);
            }
        }

        return $this;
    }
}
