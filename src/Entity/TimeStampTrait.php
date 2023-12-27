<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Auth\OtpVerificationController;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/verification',
            requirements: ['phoneNumber' => 'String', 'otp' => 'String'],
            controller: OtpVerificationController::class,
            description: "Verify OTP",
        )
    ]
)]
trait TimeStampTrait
{
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    #[ORM\Column(type: 'datetime', nullable:true)]
    private $deletedBy;

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist()]
    public function setCreatedAt(DateTimeImmutable $createdAt = new \DateTimeImmutable())
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate()]
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Get the value of deletedAt
     */
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    #[ORM\PreRemove()]
    public function setDeletedAt()
    {
        $this->deletedAt =  new \DateTimeImmutable();

        return $this;
    }

    /**
     * Get the value of deletedBy
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * Set the value of previosId
     *
     * @return  self
     */
    public function setDeletedBy(Account $deletedBy)
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

}