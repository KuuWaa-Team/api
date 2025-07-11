<?php

namespace App\Entity\Event;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Event\Game\RemoveGameAssertion;
use App\Controller\Event\Game\SetGameAssertionCorrect;
use App\Repository\Event\GameAssertionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameAssertionRepository::class)]

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/game-assertions/{id}/set-correct',
            controller: SetGameAssertionCorrect::class,
            description: 'Set assertion correct',
            name: 'setGameAssertionCorrect',
        ),
        new Post(
            uriTemplate: '/game-assertions/{id}/remove',
            controller: RemoveGameAssertion::class,
            description: 'Add game question',
            name: 'removeGameAssertion',
        ),
    ]
)]
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

    /**
     * @var Collection<int, GameResponse>
     */
    #[ORM\OneToMany(mappedBy: 'assertion', targetEntity: GameResponse::class)]
    private Collection $gameResponses;

    public function __construct()
    {
        $this->gameResponses = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, GameResponse>
     */
    public function getGameResponses(): Collection
    {
        return $this->gameResponses;
    }

    public function addGameResponse(GameResponse $gameResponse): static
    {
        if (!$this->gameResponses->contains($gameResponse)) {
            $this->gameResponses->add($gameResponse);
            $gameResponse->setAssertion($this);
        }

        return $this;
    }

    public function removeGameResponse(GameResponse $gameResponse): static
    {
        if ($this->gameResponses->removeElement($gameResponse)) {
            // set the owning side to null (unless already changed)
            if ($gameResponse->getAssertion() === $this) {
                $gameResponse->setAssertion(null);
            }
        }

        return $this;
    }
}
