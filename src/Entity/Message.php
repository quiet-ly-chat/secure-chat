<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @Groups("base")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("base")
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $msgFrom;

    /**
     * @Groups("base")
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $msgTo;

    /**
     * @Groups("base")
     * @ORM\Column(type="datetime")
     */
    private $sendDate;

    /**
     * @Groups("base")
     * @ORM\Column(type="text")
     */
    private $fromMsg;

    /**
     * @Groups("base")
     * @ORM\Column(type="text")
     */
    private $toMsg;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMsgFrom(): ?User
    {
        return $this->msgFrom;
    }

    public function setMsgFrom(?User $msgFrom): self
    {
        $this->msgFrom = $msgFrom;

        return $this;
    }

    public function getMsgTo(): ?User
    {
        return $this->msgTo;
    }

    public function setMsgTo(?User $msgTo): self
    {
        $this->msgTo = $msgTo;

        return $this;
    }

    public function getSendDate(): ?\DateTimeInterface
    {
        return $this->sendDate;
    }

    public function setSendDate(\DateTimeInterface $sendDate): self
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    public function getFromMsg(): ?string
    {
        return $this->fromMsg;
    }

    public function setFromMsg(string $fromMsg): self
    {
        $this->fromMsg = $fromMsg;

        return $this;
    }

    public function getToMsg(): ?string
    {
        return $this->toMsg;
    }

    public function setToMsg(string $toMsg): self
    {
        $this->toMsg = $toMsg;

        return $this;
    }
}
