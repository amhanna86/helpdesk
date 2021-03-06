<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="This email is already in use."
 * )
 */
class User implements UserInterface
{

    public const FIRST_LEVEL_SUPPORT='1';
    public const SECOND_LEVEL_SUPPORT='2';

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=UserType::class)
     */
    private $userType;

    /**
     * @ORM\Column(type="integer")
     */
    private $supportLevel = self::FIRST_LEVEL_SUPPORT;


//    /**
//     * @ORM\OneToMany(targetEntity=TicketComment::class, mappedBy="user")
//     */
//    private $ticketComments;
//
//    public function __construct()
//    {
//        $this->ticketComments = new ArrayCollection();
//    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email ;
    }

    /**
     * @return string
     */
    public function getFullUsername(): string
    {
        return (string) $this->firstName . ' ' . $this->lastName ;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPhone(): ?int
    {
        return $this->phone;
    }

    /**
     * @param int|null $phone
     * @return $this
     */
    public function setPhone(?int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     * @return $this
     */
    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getUserType(): ?UserType
    {
        return $this->userType;
    }

    public function setUserType(?UserType $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

//    /**
//     * @return Collection|TicketComment[]
//     */
//    public function getTicketComments(): Collection
//    {
//        return $this->ticketComments;
//    }
//
//    public function addTicketComment(TicketComment $ticketComment): self
//    {
//        if (!$this->ticketComments->contains($ticketComment)) {
//            $this->ticketComments[] = $ticketComment;
//            $ticketComment->setUser($this);
//        }
//
//        return $this;
//    }
//
//    public function removeTicketComment(TicketComment $ticketComment): self
//    {
//        if ($this->ticketComments->contains($ticketComment)) {
//            $this->ticketComments->removeElement($ticketComment);
//            // set the owning side to null (unless already changed)
//            if ($ticketComment->getUser() === $this) {
//                $ticketComment->setUser(null);
//            }
//        }
//
//        return $this;
//    }

public function getSupportLevel(): ?int
{
    return $this->supportLevel;
}

public function setSupportLevel(int $supportLevel): self
{
    $this->supportLevel = $supportLevel;

    return $this;
}
}