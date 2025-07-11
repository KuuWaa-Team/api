<?php

namespace App\Entity\Event;

use App\Entity\Invitation;
use App\Repository\Event\GameResponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameResponseRepository::class)]
class GameResponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'gameResponses')]
    private ?Invitation $invitation = null;

    #[ORM\ManyToOne(inversedBy: 'gameResponses')]
    private ?GameQuestion $question = null;

    #[ORM\ManyToOne(inversedBy: 'gameResponses')]
    private ?GameAssertion $assertion = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $answeredAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }

    public function setInvitation(?Invitation $invitation): static
    {
        $this->invitation = $invitation;

        return $this;
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

    public function getAssertion(): ?GameAssertion
    {
        return $this->assertion;
    }

    public function setAssertion(?GameAssertion $assertion): static
    {
        $this->assertion = $assertion;

        return $this;
    }

    public function getAnsweredAt(): ?\DateTimeImmutable
    {
        return $this->answeredAt;
    }

    public function setAnsweredAt(?\DateTimeImmutable $answeredAt): static
    {
        $this->answeredAt = $answeredAt;

        return $this;
    }
}
