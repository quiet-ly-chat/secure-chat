<?php
/**
 * Created by PhpStorm.
 * User: gaarunia
 * Date: 03.04.19
 * Time: 18:04
 */

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @Groups("base")
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Groups("private")
     * @ORM\Column(type="text")
     */
    private $privateKey;

    /**
     * @Groups("detailed")
     * @ORM\Column(type="text")
     */
    private $publicKey;

    /**
     * @Groups("private")
     * @ORM\OneToOne(targetEntity="App\Entity\Username", inversedBy="user")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chosenUsername;

    public function setUsername($username)
    {
        parent::setUsername($username);
        $this->setEmail($username);

        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(string $privateKey): self
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function getChosenUsername(): ?Username
    {
        return $this->chosenUsername;
    }

    public function setChosenUsername(Username $chosenUsername): self
    {
        $this->chosenUsername = $chosenUsername;

        return $this;
    }

}