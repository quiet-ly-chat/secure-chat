<?php
/**
 * @category Quiet.ly.
 * @author   Agata Drozdek <agata.drozdek@gmail.com>
 * @date     03.05.19
 */

namespace App\Manager;

use App\Entity\Friend;
use App\Entity\User;
use App\Repository\FriendRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerInterface;

/**
 * Class FriendshipManager
 * @package App\Manager
 */
class FriendshipManager
{
    /**
     * @var FriendRepository
     */
    private $friendRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FriendshipManager constructor.
     * @param LoggerInterface $logger
     * @param FriendRepository $friendRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(LoggerInterface $logger, FriendRepository $friendRepository, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->friendRepository = $friendRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param string $newFriendUsername
     * @param User $user
     * @return bool
     */
    public function createFriendship(string $newFriendUsername, User $user): bool
    {
        try {
            if ($newFriendUsername == $user->getUsername()) {
                return false;
            }

            $accepter = $this->userRepository->findOneBy(['username' => $newFriendUsername]);

            if (!$accepter instanceof User) {
                return false;
            }

            $receivedFriendRequest = $this->friendRepository->findReceivedFriendRequest($user, $accepter);

            if ($receivedFriendRequest) {
                if (!$receivedFriendRequest->getAccepted()) {
                    $receivedFriendRequest->setAccepted(true);
                    $this->entityManager->persist($receivedFriendRequest);
                    $this->entityManager->flush();
                }

                return true;
            }

            $sentFriendRequest = $this->friendRepository->findSentFriendRequest($user, $accepter);

            if ($sentFriendRequest) {
                return false;
            }

            $friend = new Friend();
            $friend->setSender($user);
            $friend->setAccepter($accepter);
            $friend->setAccepted(false);

            $this->entityManager->persist($friend);
            $this->entityManager->flush();
        } catch (NonUniqueResultException $exception) {
            $this->logger->critical($exception->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param string $usernameToRemove
     * @param User $user
     * @return bool
     */
    public function removeFriendship(string $usernameToRemove, User $user): bool
    {
        try {
            $friendToRemove = $this->userRepository->findOneBy(['username' => $usernameToRemove]);

            if (!$friendToRemove instanceof User) {
                return false;
            }
            $receivedFriendRequest = $this->friendRepository->findReceivedFriendRequest($user, $friendToRemove);

            if ($receivedFriendRequest) {
                $this->entityManager->remove($receivedFriendRequest);
                $this->entityManager->flush();

                return true;
            }

            $sentFriendRequest = $this->friendRepository->findSentFriendRequest($user, $friendToRemove);
            if ($sentFriendRequest) {
                $this->entityManager->remove($sentFriendRequest);
                $this->entityManager->flush();

                return true;
            }
        } catch (NonUniqueResultException $exception) {
            $this->logger->critical($exception->getMessage());
            return false;
        }

        return true;
    }
}