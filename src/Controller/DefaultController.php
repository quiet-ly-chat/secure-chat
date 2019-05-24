<?php
/**
 * Created by PhpStorm.
 * User: gaarunia
 * Date: 03.04.19
 * Time: 17:39
 */

namespace App\Controller;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).*"}, name="index")
     * @return Response
     */
    public function indexAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('base.html.twig', [
            'isAuthenticated' => json_encode(is_object($user)),
            'roles'           => json_encode(is_object($user) ? $user->getRoles() : []),
            'privateKey'      => json_encode(is_object($user) ? $user->getPrivateKey() : []),
            'publicKey'       => json_encode(is_object($user) ? $user->getPublicKey() : []),
            'username'        => json_encode(is_object($user) ? $user->getUsername() : []),
            'userId'          => json_encode(is_object($user) ? $user->getId() : []),
        ]);
    }
}