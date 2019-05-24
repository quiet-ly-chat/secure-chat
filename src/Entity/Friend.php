<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FriendRepository")
 */
class Friend
{

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="friends")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $sender;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="accepterFriends")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $accepter;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accepted;

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getAccepter(): ?User
    {
        return $this->accepter;
    }

    public function setAccepter(?User $accepter): self
    {
        $this->accepter = $accepter;

        return $this;
    }

    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }
}
