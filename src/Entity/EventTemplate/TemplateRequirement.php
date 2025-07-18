<?php

namespace App\Entity\EventTemplate;

use App\Entity\Event\EventTemplate;
use App\Repository\EventTemplate\TemplateRequirementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TemplateRequirementRepository::class)]
class TemplateRequirement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'templateRequirements')]
    private ?EventTemplate $template = null;

    /**
     * @var Collection<int, TemplateRequirementValue>
     */
    #[ORM\OneToMany(mappedBy: 'requirement', targetEntity: TemplateRequirementValue::class)]
    #[Groups(['event:pRead', 'event:fRead', 'invitation:fRead', 'templates:fRead'])]
    private Collection $templateRequirementValues;

    public function __construct()
    {
        $this->templateRequirementValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
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

    public function getTemplate(): ?EventTemplate
    {
        return $this->template;
    }

    public function setTemplate(?EventTemplate $template): static
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return Collection<int, TemplateRequirementValue>
     */
    public function getTemplateRequirementValues(): Collection
    {
        return $this->templateRequirementValues;
    }

    public function addTemplateRequirementValue(TemplateRequirementValue $templateRequirementValue): static
    {
        if (!$this->templateRequirementValues->contains($templateRequirementValue)) {
            $this->templateRequirementValues->add($templateRequirementValue);
            $templateRequirementValue->setRequirement($this);
        }

        return $this;
    }

    public function removeTemplateRequirementValue(TemplateRequirementValue $templateRequirementValue): static
    {
        if ($this->templateRequirementValues->removeElement($templateRequirementValue)) {
            // set the owning side to null (unless already changed)
            if ($templateRequirementValue->getRequirement() === $this) {
                $templateRequirementValue->setRequirement(null);
            }
        }

        return $this;
    }
}
