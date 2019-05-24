<?php

namespace App\Controller;

use App\Manager\FriendshipManager;
use App\Repository\FriendRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ApiFriendController
 * @package App\Controller
 * @Route("/api/friend")
 * @IsGranted("ROLE_USER")
 */
class ApiFriendController extends AbstractController
{
    use Form;

    /**
     * @Route("/")
     * @param FriendRepository $friendRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getFriendsAction(FriendRepository $friendRepository, SerializerInterface $serializer)
    {
        $friends = $friendRepository->findAllForUser($this->getUser());

        return new JsonResponse($serializer->serialize($friends, 'json'));
    }

    /**
     * @Route("/new", methods="POST")
     * @param Request $request
     * @param FriendshipManager $friendshipManager
     * @return JsonResponse
     */
    public function createFriendAction(Request $request, FriendshipManager $friendshipManager)
    {
        $data = $this->getRequestBody($request);

        if (!array_key_exists('username', $data)) {
            return new JsonResponse(['status' => 'Error', 'message' => 'Missing username'], 400);
        }

        $friendshipManager->createFriendship($data['username'], $this->getUser());

        return new JsonResponse();
    }

    /**
     * @Route("/{username}", methods="DELETE")
     * @param Request $request
     * @param FriendshipManager $friendshipManager
     * @return JsonResponse
     */
    public function deleteFriendAction(Request $request, FriendshipManager $friendshipManager)
    {
        $username = $request->get('username');

        if (!$username) {
            return new JsonResponse('Missing username', 400);
        }

        $result = $friendshipManager->removeFriendship($username, $this->getUser());

        return new JsonResponse(['status' => $result ? 'Success' : 'Error'], 200);
    }
}