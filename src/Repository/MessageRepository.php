<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    /**
     * MessageRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param int $userId
     * @param int $user2Id
     * @return Message[] Returns an array of Message objects
     */
    public function findForFriends(int $userId, int $user2Id)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('(m.msgFrom = :user1 AND m.msgTo = :user2) OR (m.msgFrom = :user2 AND m.msgTo = :user1)')
            ->setParameter('user1', $userId)
            ->setParameter('user2', $user2Id)
            ->orderBy('m.sendDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
