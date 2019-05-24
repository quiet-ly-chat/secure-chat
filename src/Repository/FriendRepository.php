<?php

namespace App\Repository;

use App\Entity\Friend;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Friend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friend[]    findAll()
 * @method Friend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRepository extends ServiceEntityRepository
{
    /**
     * FriendRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Friend::class);
    }

    /**
     * @param User $user
     * @return Friend[]
     */
    public function findAllForUser(User $user)
    {
        $sentFriends = $this->createQueryBuilder('f')
            ->andWhere('f.sender = :user AND f.accepted = :accepted')
            ->setParameter('user', [$user->getId()])
            ->setParameter('accepted', 1)
            ->join('f.accepter', 'accepterUser')
            ->join('f.sender', 'senderUser')
            ->select('accepterUser.id as friendId, accepterUser.username AS friendUsername, senderUser.id AS senderId, accepterUser.id AS accepterId, accepterUser.publicKey AS friendPubKey')
            ->getQuery()
            ->getResult();

        $receivedFriends = $this->createQueryBuilder('f')
            ->andWhere('f.accepter = :user AND f.accepted = :accepted')
            ->setParameter('user', [$user->getId()])
            ->setParameter('accepted', 1)
            ->join('f.sender', 'senderUser')
            ->join('f.accepter', 'accepterUser')
            ->select('senderUser.id AS friendId, senderUser.username AS friendUsername, senderUser.id AS senderId, accepterUser.id AS accepterId, senderUser.publicKey AS friendPubKey')
            ->getQuery()
            ->getResult();

        return array_merge($sentFriends, $receivedFriends);
    }

    /**
     * @param User $user
     * @return Friend[]
     */
    public function findAllForUserWithMessages(User $user)
    {
        $sentFriends = $this->createQueryBuilder('f')
            ->andWhere('f.sender = :user AND f.accepted = :accepted')
            ->setParameter('user', [$user->getId()])
            ->setParameter('accepted', 1)
            ->join('f.accepter', 'accepterUser')
            ->join('f.sender', 'senderUser')
            ->select('accepterUser.id as friendId, accepterUser.username AS friendUsername, senderUser.id AS senderId, accepterUser.id AS accepterId')
            ->getQuery()
            ->getResult();

        $receivedFriends = $this->createQueryBuilder('f')
            ->andWhere('f.accepter = :user AND f.accepted = :accepted')
            ->setParameter('user', [$user->getId()])
            ->setParameter('accepted', 1)
            ->join('f.sender', 'senderUser')
            ->join('f.accepter', 'accepterUser')
            ->select('senderUser.id AS friendId, senderUser.username AS friendUsername, senderUser.id AS senderId, accepterUser.id AS accepterId')
            ->getQuery()
            ->getResult();

        return array_merge($sentFriends, $receivedFriends);
    }

    /**
     * @param User $currentUser
     * @param User $friend
     * @return Friend
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findReceivedFriendRequest(User $currentUser, User $friend)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.sender = :friend AND f.accepter = :user')
            ->setParameter('friend', [$friend->getId()])
            ->setParameter('user', [$currentUser->getId()])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param User $currentUser
     * @param User $friend
     * @return Friend
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSentFriendRequest(User $currentUser, User $friend)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.sender = :user AND f.accepter = :friend')
            ->setParameter('friend', [$friend->getId()])
            ->setParameter('user', [$currentUser->getId()])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
