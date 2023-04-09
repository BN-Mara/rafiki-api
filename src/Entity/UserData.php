<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Elasticsearch\Filter\TermFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operations;
use App\Repository\UserDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

#[ORM\Entity(repositoryClass: UserDataRepository::class)]
#[ApiResource(operations:[new Get(),new Post(),new Put(),new GetCollection()])]
#[ApiFilter(SearchFilter::class,properties:['uid'=>'exact', 'username'=>'exact','email'=>'exact'])]
class UserData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePhoto = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 10)]
    private ?string $userType = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $registeredBy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(length: 64)]
    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'userData')]
    private ?Tutor $tutorId = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $province = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $deviceToken = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $uid = null;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: VideoData::class)]
    private Collection $videoData;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $language = null;

    #[ORM\Column(length: 255)]
    private ?string $churchFile = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $churchName = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $churchAddress = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $pastorName = null;

    public function __construct()
    {
        //$this->cretedAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        $this->videoData = new ArrayCollection();
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

    public function getProfilePhoto(): ?string
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto(?string $profilePhoto): self
    {
        $this->profilePhoto = $profilePhoto;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    public function getRegisteredBy(): ?string
    {
        return $this->registeredBy;
    }

    public function setRegisteredBy(?string $registeredBy): self
    {
        $this->registeredBy = $registeredBy;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getTutorId(): ?Tutor
    {
        return $this->tutorId;
    }

    public function setTutorId(?Tutor $tutorId): self
    {
        $this->tutorId = $tutorId;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getDeviceToken(): ?string
    {
        return $this->deviceToken;
    }

    public function setDeviceToken(?string $deviceToken): self
    {
        $this->deviceToken = $deviceToken;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return Collection<int, VideoData>
     */
    public function getVideoData(): Collection
    {
        return $this->videoData;
    }

    public function addVideoData(VideoData $videoData): self
    {
        if (!$this->videoData->contains($videoData)) {
            $this->videoData->add($videoData);
            $videoData->setUserId($this);
        }

        return $this;
    }

    public function removeVideoData(VideoData $videoData): self
    {
        if ($this->videoData->removeElement($videoData)) {
            // set the owning side to null (unless already changed)
            if ($videoData->getUserId() === $this) {
                $videoData->setUserId(null);
            }
        }

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getChurchFile(): ?string
    {
        return $this->churchFile;
    }

    public function setChurchFile(string $churchFile): self
    {
        $this->churchFile = $churchFile;

        return $this;
    }

    public function getChurchName(): ?string
    {
        return $this->churchName;
    }

    public function setChurchName(?string $churchName): self
    {
        $this->churchName = $churchName;

        return $this;
    }

    public function getChurchAddress(): ?string
    {
        return $this->churchAddress;
    }

    public function setChurchAddress(?string $churchAddress): self
    {
        $this->churchAddress = $churchAddress;

        return $this;
    }

    public function getPastorName(): ?string
    {
        return $this->pastorName;
    }

    public function setPastorName(?string $pastorName): self
    {
        $this->pastorName = $pastorName;

        return $this;
    }
}
