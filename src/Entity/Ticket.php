<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;


/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{
    use TimestampableEntity;

    public const NEW = 0;
    public const INPROGRESS = 1;
    public const DONE = 2;
    public const ARCHIVED = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=TicketComment::class, mappedBy="ticket")
     */
    private $ticketComments;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="agentTickets")
     */
    private $agent;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="customerTickets")
     */
    private $customer;

    /**
     * Ticket constructor.
     */
    public function __construct()
    {
        $this->ticketComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|TicketComment[]
     */
    public function getTicketComments(): Collection
    {
        return $this->ticketComments;
    }

    public function addTicketComment(TicketComment $ticketComment): self
    {
        if (!$this->ticketComments->contains($ticketComment)) {
            $this->ticketComments[] = $ticketComment;
            $ticketComment->setTicket($this);
        }

        return $this;
    }

    public function removeTicketComment(TicketComment $ticketComment): self
    {
        if ($this->ticketComments->contains($ticketComment)) {
            $this->ticketComments->removeElement($ticketComment);
            // set the owning side to null (unless already changed)
            if ($ticketComment->getTicket() === $this) {
                $ticketComment->setTicket(null);
            }
        }

        return $this;
    }

    public function getAgent(): ?User
    {
        return $this->agent;
    }

    public function setAgent(?User $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
