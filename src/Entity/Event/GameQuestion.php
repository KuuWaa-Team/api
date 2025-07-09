<?php

namespace App\Entity\Event;

use App\Repository\Event\GameQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameQuestionRepository::class)]
class GameQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    /**
     * @var Collection<int, GameAssertion>
     */
    #[ORM\OneToMany(mappedBy: 'question', targetEntity: GameAssertion::class)]
    private Collection $gameAssertions;

    public function __construct()
    {
        $this->gameAssertions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, GameAssertion>
     */
    public function getGameAssertions(): Collection
    {
        return $this->gameAssertions;
    }

    public function addGameAssertion(GameAssertion $gameAssertion): static
    {
        if (!$this->gameAssertions->contains($gameAssertion)) {
            $this->gameAssertions->add($gameAssertion);
            $gameAssertion->setQuestion($this);
        }

        return $this;
    }

    public function removeGameAssertion(GameAssertion $gameAssertion): static
    {
        if ($this->gameAssertions->removeElement($gameAssertion)) {
            // set the owning side to null (unless already changed)
            if ($gameAssertion->getQuestion() === $this) {
                $gameAssertion->setQuestion(null);
            }
        }

        return $this;
    }
}
