<?php
/**
 * @category Quiet.ly.
 * @author   Agata Drozdek <agata.drozdek@gmail.com>
 * @date     16.05.19
 */

namespace App\Controller;

use App\Entity\User;
use App\Repository\FriendRepository;
use App\Repository\MessageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ApiMessageController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 * @Route("/api/message")
 */
class ApiMessageController extends AbstractController
{
    /**
     * @var MessageRepository
     */
    private $messageRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ApiMessageController constructor.
     * @param MessageRepository $messageRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(MessageRepository $messageRepository, SerializerInterface $serializer)
    {
        $this->messageRepository = $messageRepository;
        $this->serializer = $serializer;
    }


    /**
     * @Route("/")
     * @param FriendRepository $friendRepository
     * @return JsonResponse
     */
    public function getForAllFriends(FriendRepository $friendRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $friends = $friendRepository->findAllForUser($user);

        $messages = [];
        foreach ($friends as $friend) {
            $messages['fr' . $friend['friendId']] = $this->messageRepository->findForFriends($user->getId(), $friend['friendId']);
        }

        return new JsonResponse($this->serializer->serialize($messages, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
            'groups'                     => ['base'],
        ]));
    }

    /**
     * @Route("/{friendId}", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getForFriend(Request $request): JsonResponse
    {
        $friendId = $request->get('friendId');

        /** @var User $user */
        $user = $this->getUser();
        $messages = $this->messageRepository->findForFriends($user->getId(), $friendId);

        return new JsonResponse($messages);
    }
}