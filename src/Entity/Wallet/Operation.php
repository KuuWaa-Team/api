<?php

namespace App\Entity\Wallet;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Auth\LoginController;
use App\Controller\Event\ListingUserEventController;
use App\Controller\Wallet\CheckPaymentStatusController;
use App\Controller\Wallet\RechargeController;
use App\Entity\User;
use App\Repository\Wallet\OperationRepository;
use App\Service\WalletService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OperationRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/operations/{id}/check-payment-status',
            controller: CheckPaymentStatusController::class,
            description: "Check payment Status",
        ),
    ]
)]
class Operation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['operation:pRead'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'operations')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?int $credits = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?string $paymentMethod = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?string $orderNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['operation:pRead'])]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
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

    public function getCredits(): ?int
    {
        return $this->credits;
    }

    public function setCredits(?int $credits): static
    {
        $this->credits = $credits;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

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

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isPaid (): bool {
        return $this->getStatus() === WalletService::$STATUS_PAID;
    }
}
