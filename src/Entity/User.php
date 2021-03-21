<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email is already in use."
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\Column(type="string", length=40)
     * @Assert\Regex(
     *  pattern = "/^[\w]{2,20}$/",
     *  message = "Only number or letters"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *  pattern = "/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[*.!@$%^&(){}:;<>,.?~_+-=|[\/\\\]]).{6,15}$/", 
     *  message = "not valide password"
     * )
 
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="The passwords are not the same")
     */
    private $verifPassword;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=30,nullable=true)
     * @Assert\Regex(
     *  pattern = "/^[\w]{2,20}$/",
     *  message = "Only number or letters"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=30,nullable=true)
     * @Assert\Regex(
     *  pattern = "/^[\w]{2,20}$/",
     *  message = "Only number or letters"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $githubToken;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $githubId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id42;

    /**
     * @ORM\OneToMany(targetEntity=UserPhoto::class, mappedBy="user")
     */
    private $userPhoto;


    public function __construct()
    {
        $this->userPhoto = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getVerifPassword(): ?string
    {
        return $this->verifPassword;
    }

    public function setVerifPassword(?string $verifPassword): self
    {
        $this->verifPassword = $verifPassword;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(string $role): self
    {
        array_push($this->roles, $role);
        return $this;
    }

    public function getSalt()
    {
        
    }

    public function eraseCredentials()
    {
        
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getGithubToken(): ?string
    {
        return $this->githubToken;
    }

    public function setGithubToken(?string $githubToken): self
    {
        $this->githubToken = $githubToken;

        return $this;
    }

    public function getGithubId(): ?int
    {
        return $this->githubId;
    }

    public function setGithubId(?int $githubId): self
    {
        $this->githubId = $githubId;
        var_dump("in entity");
        return $this;
    }


    public function getId42(): ?int
    {
        return $this->id42;
    }

    public function setId42(?int $id42): self
    {
        $this->id42 = $id42;

        return $this;
    }

    /**
     * @return Collection|UserPhoto[]
     */
    public function getUserPhoto(): Collection
    {
        return $this->userPhoto;
    }

    public function addUserPhoto(UserPhoto $userPhoto): self
    {
        if (!$this->userPhoto->contains($userPhoto)) {
            $this->userPhoto[] = $userPhoto;
            $userPhoto->setUser($this);
        }

        return $this;
    }

    public function removeUserPhoto(UserPhoto $userPhoto): self
    {
        if ($this->userPhoto->removeElement($userPhoto)) {
            // set the owning side to null (unless already changed)
            if ($userPhoto->getUser() === $this) {
                $userPhoto->setUser(null);
            }
        }

        return $this;
    }
 
}
