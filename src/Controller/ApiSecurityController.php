<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UsernameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiSecurityController
 * @package App\Controller
 */
final class ApiSecurityController extends AbstractController
{
    use Form;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $adminUsername;

    /**
     * ApiSecurityController constructor.
     * @param LoggerInterface $logger
     * @param string $adminUsername
     */
    public function __construct(LoggerInterface $logger, string $adminUsername)
    {
        $this->logger = $logger;
        $this->adminUsername = $adminUsername;
    }

    /**
     * @Route("/api/security/login", name="login")
     * @return JsonResponse
     */
    public function loginAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $response = new JsonResponse($user ? [
            'roles'      => $user->getRoles(),
            'privateKey' => $user->getPrivateKey(),
            'publicKey'  => $user->getPublicKey(),
        ] : []);
        return $response;
    }

    /**
     * @Route("/api/security/register", name="registration")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UsernameRepository $usernameRepository
     * @return JsonResponse
     */
    public function registerAction(Request $request, EntityManagerInterface $entityManager, UsernameRepository $usernameRepository)
    {
        try {
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);

            $this->processForm($request, $form);
            if ($form->isSubmitted() && $form->isValid()) {

                $user->setChosenUsername($usernameRepository->findOneBy(['username' => $user->getUsername()]));
                $user->setEnabled(true);
                $roles = ['ROLE_USER'];
                if ($this->adminUsername && $user->getUsername() == $this->adminUsername) {
                    $roles[] = 'ROLE_ADMIN';
                }
                $user->setRoles($roles);
                $entityManager->persist($user);
                $entityManager->flush();
                return new JsonResponse(sprintf('User %s successfully created', $user->getUsername()));

            } else {
                $data = [
                    'type'   => 'validation_error',
                    'title'  => 'There was a validation error',
                    'errors' => $this->getErrorsFromForm($form),
                ];
                return new JsonResponse($data, 400);

            }

        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            return new JsonResponse(null, 400);
        }

    }


    /**
     * @Route("/api/security/logout", name="logout")
     * @return void
     * @throws \RuntimeException
     */
    public function logoutAction(): void
    {
        throw new \RuntimeException('This should not be reached!');
    }
}