<?php

namespace App\Entity;

use App\Repository\UserPhotoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserPhotoRepository::class)
 */
class UserPhoto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photoLink;

    /**
     * @ORM\Column(type="boolean")
     */
    private $profilePhoto;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userPhoto")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getPhotoLink(): ?string
    {
        return $this->photoLink;
    }

    public function setPhotoLink(string $photoLink): self
    {
        $this->photoLink = $photoLink;

        return $this;
    }

    public function getProfilePhoto(): ?bool
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto(bool $profilePhoto): self
    {
        $this->profilePhoto = $profilePhoto;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
