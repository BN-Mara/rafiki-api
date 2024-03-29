<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\VideoDataRepository;
use App\State\VideoStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoDataRepository::class)]
#[ApiResource(operations:[new Get(),new Post(),new Put(),new GetCollection()], order: ['createdAt' => 'DESC'])]
#[Post(processor: VideoStateProcessor::class)]
#[ApiFilter(SearchFilter::class,properties:['username'=>'exact','primeId'=>'exact','userId'=>'exact'])]
class VideoData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'videoData')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserData $userId = null;

    #[ORM\Column(nullable: true)]
    private ?int $shareCount = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $style = null;

    #[ORM\Column(length: 255)]
    private ?string $videoUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePhoto = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $likes = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $views = [];

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $primeId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $songName = null;

    #[ORM\OneToMany(mappedBy: 'video', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        $this->comments = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserId(): ?UserData
    {
        return $this->userId;
    }

    public function setUserId(?UserData $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getShareCount(): ?int
    {
        return $this->shareCount;
    }

    public function setShareCount(?int $shareCount): self
    {
        $this->shareCount = $shareCount;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(?string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(string $videoUrl): self
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

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

    public function getLikes(): array
    {
        return $this->likes;
    }

    public function setLikes(array $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getViews(): array
    {
        return $this->views;
    }

    public function setViews(?array $views): self
    {
        $this->views = $views;

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

    public function getPrimeId(): ?int
    {
        return $this->primeId;
    }

    public function setPrimeId(int $primeId): self
    {
        $this->primeId = $primeId;

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

    public function getSongName(): ?string
    {
        return $this->songName;
    }

    public function setSongName(?string $songName): self
    {
        $this->songName = $songName;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setVideo($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getVideo() === $this) {
                $comment->setVideo(null);
            }
        }

        return $this;
    }
}
