<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Auth\OtpVerificationController;
use App\Repository\OtpRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OtpRepository::class)]

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/otps/verification',
            requirements: ['otp' => 'String'],
            controller: OtpVerificationController::class,
            description: "Otp Verification",
        )
    ]
)]
class Otp
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'otps')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $otp = null;

    public function __construct()
    {
        $this->setCreatedAt();
    }

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

    public function getOtp(): ?string
    {
        return $this->otp;
    }

    public function setOtp(?string $otp): static
    {
        $this->otp = $otp;

        return $this;
    }
}
