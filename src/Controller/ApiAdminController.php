<?php


namespace App\Controller;

use App\Entity\Username;
use App\Form\UsernameType;
use App\Repository\UsernameRepository;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/admin")
 * Class ApiAdminController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */
class ApiAdminController extends AbstractController
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
     * ApiAdminController constructor.
     * @param LoggerInterface $logger
     * @param string $adminUsername
     */
    public function __construct(LoggerInterface $logger, string $adminUsername)
    {
        $this->logger = $logger;
        $this->adminUsername = $adminUsername;
    }

    /**
     * @Route("/username")
     * @param UsernameRepository $usernameRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function listUsernamesAction(UsernameRepository $usernameRepository, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($usernameRepository->findAll(), 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return new JsonResponse($data);
    }

    /**
     * @Route("/username/new", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createUsername(Request $request)
    {
        try {
            $username = new Username();
            $form = $this->createForm(UsernameType::class, $username);

            $this->processForm($request, $form);
            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($username);
                $entityManager->flush();
                return new JsonResponse(sprintf('Username successfully created'));

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
     * @Route("/username/{username}", methods={"DELETE"})
     * @param Request $request
     * @param UsernameRepository $usernameRepository
     * @return JsonResponse
     */
    public function deleteUsername(Request $request, UsernameRepository $usernameRepository)
    {
        //        if ($this->isCsrfTokenValid('delete' . $username->getId(), $request->request->get('_token'))) {

        $username = $request->get('username');

        if ($username == $this->adminUsername) {
            return new JsonResponse(null, 400);
        }

        $username = $usernameRepository->findOneBy([
            'username' => $request->get(
                'username'
            ),
        ]);

        if ($username) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($username);
            $entityManager->flush();

            return new JsonResponse(sprintf('Username successfully removed'));
        }
//        }
        return new JsonResponse(null, 400);
    }
}