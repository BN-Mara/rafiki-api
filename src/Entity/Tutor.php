<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TutorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorRepository::class)]
#[ApiResource]
class Tutor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $letterUrl = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'tutorId', targetEntity: UserData::class)]
    private Collection $userData;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        $this->userData = new ArrayCollection();
    }
   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLetterUrl(): ?string
    {
        return $this->letterUrl;
    }

    public function setLetterUrl(?string $letterUrl): self
    {
        $this->letterUrl = $letterUrl;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, UserData>
     */
    public function getUserData(): Collection
    {
        return $this->userData;
    }

    public function addUserData(UserData $userData): self
    {
        if (!$this->userData->contains($userData)) {
            $this->userData->add($userData);
            $userData->setTutorId($this);
        }

        return $this;
    }

    public function removeUserData(UserData $userData): self
    {
        if ($this->userData->removeElement($userData)) {
            // set the owning side to null (unless already changed)
            if ($userData->getTutorId() === $this) {
                $userData->setTutorId(null);
            }
        }

        return $this;
    }
}
