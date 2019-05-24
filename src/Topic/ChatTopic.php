<?php

namespace App\Topic;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ChatTopic
 * @package App\Topic
 */
class ChatTopic implements TopicInterface
{
    /**
     * @var ClientManipulatorInterface
     */
    private $clientManipulator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ChatTopic constructor.
     * @param ClientManipulatorInterface $clientManipulator
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ClientManipulatorInterface $clientManipulator,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        SerializerInterface $serializer
    )
    {
        $this->clientManipulator = $clientManipulator;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        if (!$this->checkIfActionAllowed($request, $connection)) {
            $topic->remove($connection);
        };

        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId . " has joined " . $topic->getId()]);
    }

    /**
     * @param WampRequest $request
     * @param ConnectionInterface $connection
     * @return bool
     */
    protected function checkIfActionAllowed(WampRequest $request, ConnectionInterface $connection): bool
    {
        $user = $this->getUser($connection);

        if (!$user instanceof User) {
            return false;
        }

        $roomNr = $request->getAttributes()->get('room');
        $explodedUsers = explode('-', $roomNr);

        if (!in_array($user->getId(), $explodedUsers)) {
            return false;
        }

        return true;
    }

    /**
     * @param ConnectionInterface $connection
     * @return User|null
     */
    protected function getUser(ConnectionInterface $connection): ?User
    {
        $user = $this->clientManipulator->getClient($connection);

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId . " has left " . $topic->getId()]);
    }

    /**
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @param $event
     * @param array $exclude
     * @param array $eligible
     * @throws \Exception
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        if (!$this->checkIfActionAllowed($request, $connection)) {
            $topic->remove($connection);
            return;
        };
        $user = $this->getUser($connection);

        $roomNr = $request->getAttributes()->get('room');
        $explodedUsers = explode('-', $roomNr);
        $toUser = null;
        foreach ($explodedUsers as $explodedUser) {
            if ($explodedUser != $user->getId()) {
                $toUser = $explodedUser;
            }
        }

        if (array_key_exists('removeFriend', $event)) {
            $topic->broadcast($event);
        } else {
            $message = new Message();
            $message->setSendDate(new \DateTime());
            $message->setToMsg($event['forFriend']);
            $message->setFromMsg($event['forUser']);
            $message->setMsgFrom($this->userRepository->findOneBy(['id' => $user->getId()]));
            $message->setMsgTo($this->userRepository->findOneBy(['id' => $toUser]));

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $topic->broadcast([
                'chatEvent' => $this->serializer->serialize($message, 'json', ['groups' => 'base']),
            ]);
        }
    }

    /**
     * Like RPC is will use to prefix the channel
     * @return string
     */
    public function getName()
    {
        return 'chat.topic';
    }
}