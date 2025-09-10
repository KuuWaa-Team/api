<?php

namespace App\Entity;

use App\Repository\NewsLetterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NewsLetterRepository::class)]
class NewsLetter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['newsletter:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['newsletter:fRead'])]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['newsletter:fRead'])]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['newsletter:fRead'])]
    private ?bool $isActif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(?bool $isActif): static
    {
        $this->isActif = $isActif;

        return $this;
    }
}
