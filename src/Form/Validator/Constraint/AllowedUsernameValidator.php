<?php


namespace App\Form\Validator\Constraint;

use App\Entity\Username;
use App\Repository\UsernameRepository;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Class AllowedUsernameValidator
 * @package App\Form\Validator
 */
class AllowedUsernameValidator extends ConstraintValidator
{
    /**
     * @var string
     */
    protected $adminUsername;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UsernameRepository
     */
    private $usernameRepository;

    /**
     * AllowedUsernameValidator constructor.
     * @param UserRepository $userRepository
     * @param UsernameRepository $usernameRepository
     * @param string $adminUsername
     */
    public function __construct(UserRepository $userRepository, UsernameRepository $usernameRepository, string $adminUsername)
    {
        $this->userRepository = $userRepository;
        $this->usernameRepository = $usernameRepository;
        $this->adminUsername = $adminUsername;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof AllowedUsername) {
            throw new UnexpectedTypeException($constraint, AllowedUsername::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');

        }

        $allowedUsernames = $this->usernameRepository->findAll();

        $found = false;
        /** @var Username $allowedUsername */
        foreach ($allowedUsernames as $allowedUsername) {
            if ($allowedUsername->getUsername() == $value) {
                $found = true;
                if ($allowedUsername->getUser() !== null) {
                    $this->context->buildViolation($constraint->message)
                        ->addViolation();
                }
            }
        }

        if (!$found) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}