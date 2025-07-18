<?php

namespace App\Entity\EventTemplate;

use App\Entity\Event;
use App\Repository\EventTemplate\TemplateRequirementValueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TemplateRequirementValueRepository::class)]
class TemplateRequirementValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'templateRequirementValues')]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?TemplateRequirement $requirement = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead'])]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'templateRequirementValues')]
    private ?Event $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequirement(): ?TemplateRequirement
    {
        return $this->requirement;
    }

    public function setRequirement(?TemplateRequirement $requirement): static
    {
        $this->requirement = $requirement;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

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
}
