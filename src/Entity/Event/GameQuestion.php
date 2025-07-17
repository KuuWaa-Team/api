<?php

namespace App\Entity\Event;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Event\Game\AddAssertionToQuestion;
use App\Controller\Event\Game\DeleteGameQuestionController;
use App\Controller\Event\Game\EditGameQuestion;
use App\Controller\Event\Game\RemoveGameAssertion;
use App\Controller\Event\Game\SetGameAssertionCorrect;
use App\Entity\Event;
use App\Repository\Event\GameQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GameQuestionRepository::class)]

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/game-questions/{id}/add-assertion',
            controller: AddAssertionToQuestion::class,
            description: 'add assertion to question',
            name: 'addAssertionToQuestion',
        ),
        new Post(
            uriTemplate: '/game-questions/{id}/edit',
            controller: EditGameQuestion::class,
            description: 'edit game question',
            name: 'editGameQuestion',
        ),
        new Post(
            uriTemplate: '/game-questions/{id}/remove',
            controller: DeleteGameQuestionController::class,
            description: 'remove game question',
            name: 'removeGameQuestion',
        ),
    ]
)]
class GameQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['question:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['question:read', 'question:write'])]
    private ?int $position = null;

    /**
     * @var Collection<int, GameAssertion>
     */
    #[ORM\OneToMany(mappedBy: 'question', targetEntity: GameAssertion::class, cascade: ['remove'])]
    #[Groups(['question:read'])]
    private Collection $gameAssertions;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[Groups(['question:read'])]
    private ?Event $event = null;

    /**
     * @var Collection<int, GameResponse>
     */
    #[ORM\OneToMany(mappedBy: 'question', targetEntity: GameResponse::class, cascade: ['remove'])]
    private Collection $gameResponse;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    public function __construct()
    {
        $this->gameAssertions = new ArrayCollection();
        $this->gameResponse = new ArrayCollection();
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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection<int, GameResponse>
     */
    public function getAssertion(): Collection
    {
        return $this->gameResponse;
    }

    public function addAssertion(GameResponse $gameResponse): static
    {
        if (!$this->gameResponse->contains($gameResponse)) {
            $this->gameResponse->add($gameResponse);
            $gameResponse->setQuestion($this);
        }

        return $this;
    }

    public function removeAssertion(GameResponse $gameResponse): static
    {
        if ($this->gameResponse->removeElement($gameResponse)) {
            // set the owning side to null (unless already changed)
            if ($gameResponse->getQuestion() === $this) {
                $gameResponse->setQuestion(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
