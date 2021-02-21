<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    /**
     * @param UserInterface $user
     * @return Ticket[] Returns an array of Ticket objects
     */
    public function findByUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.customer = :user')
            ->orWhere('t.agent = :user')
            ->setParameter('user', $user)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param UserInterface $user
     * @return Ticket[] Returns an array of Ticket objects
     */
    public function findByUserAndStatus(UserInterface $user, $status): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.customer = :user')
            ->andWhere('t.status = :status')
            ->orWhere('t.agent = :user')
            ->setParameter('user', $user)
            ->setParameter('status', $status)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getFirstLevelOpenTicketsCount(){
        return $this->createQueryBuilder('t')
            ->join('t.agent', 'agent')
            ->select('agent.id as userId, count(t) as ticketCount')
            ->where('t.status < 2')
            ->andWhere('agent.supportLevel = 1')
            ->groupBy('t.agent')
            ->getQuery()->getResult();
    }
}
