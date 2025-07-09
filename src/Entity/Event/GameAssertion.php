<?php

namespace App\Entity\Event;

use App\Repository\Event\GameAssertionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameAssertionRepository::class)]
class GameAssertion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'gameAssertions')]
    private ?GameQuestion $question = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $text = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isCorrect = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?GameQuestion
    {
        return $this->question;
    }

    public function setQuestion(?GameQuestion $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(?bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }
}
