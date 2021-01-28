<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TicketService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @return User|null
     */
    public function getFirstLevelSupportUser(): ?User
    {
        $usersTicketCount = $this->entityManager->getRepository(Ticket::class)->getFirstLevelOpenTicketsCount();
        $lowestUserTicketCount = array_column($usersTicketCount, 'ticketCount');
        $minArray = $usersTicketCount[array_search(min($lowestUserTicketCount), $lowestUserTicketCount)];
        return $this->entityManager->getRepository(User::class)->findOneBy(['id'=>$minArray['userId']]);
    }

}