<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
#[ApiResource]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $firstName = null;

    #[ORM\Column(length: 32)]
    private ?string $lastName = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(length: 255)]
    private ?string $coverImage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\ManyToOne(inversedBy: 'artists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Competition $competition = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated = null;
    const SERVER_PATH_TO_IMAGE_FOLDER = 'images/artists';

    /**
     * Unmapped property to handle file uploads
     */
    private ?UploadedFile $file = null;

    #[ORM\OneToMany(mappedBy: 'artist', targetEntity: Vote::class)]
    private Collection $votes;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

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

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

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

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(?Competition $competition): self
    {
        $this->competition = $competition;

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

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }
    public function setFile(?UploadedFile $file = null): void
    {
        $this->file = $file;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload(): void
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

       // we use the original file name here but you should
       // sanitize it at least to avoid any security issues

       // move takes the target directory and target filename as params
       $fname = $this->firstName.''.$this->lastName.''.$this->numero.'.jpg';
       //die(var_dump(dirname(__DIR__).self::SERVER_PATH_TO_IMAGE_FOLDER));
       $this->getFile()->move(
        self::SERVER_PATH_TO_IMAGE_FOLDER,
           $fname
       );

       // set the path property to the filename where you've saved the file
       $this->coverImage = $fname;

       // clean up the file property as you won't need it anymore
       $this->setFile(null);
   }

   /**
    * Lifecycle callback to upload the file to the server.
    */
   public function lifecycleFileUpload(): void
   {
       $this->upload();
   }

   /**
    * Updates the hash value to force the preUpdate and postUpdate events to fire.
    */
   public function refreshUpdated(): void
   {
      $this->setUpdated(new \DateTime());
      $this->lifecycleFileUpload();
   }

   // ... the rest of your class lives under here, including the generated fields
   //     such as filename and updated

   /**
    * @return Collection<int, Vote>
    */
   public function getVotes(): Collection
   {
       return $this->votes;
   }

   public function addVote(Vote $vote): self
   {
       if (!$this->votes->contains($vote)) {
           $this->votes->add($vote);
           $vote->setArtist($this);
       }

       return $this;
   }

   public function removeVote(Vote $vote): self
   {
       if ($this->votes->removeElement($vote)) {
           // set the owning side to null (unless already changed)
           if ($vote->getArtist() === $this) {
               $vote->setArtist(null);
           }
       }

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

   public function setIsActive(?bool $isActive): self
   {
       $this->isActive = $isActive;

       return $this;
   }
}
