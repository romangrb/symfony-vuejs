<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Files", mappedBy="id")
     * @JoinColumn(name="avatar_id", referencedColumnName="id")
     */
    protected $avatar;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EventParticipant", mappedBy="user", orphanRemoval=true)
     */
    private $event_participants;

    public function __construct()
    {
        parent::__construct();
        $this->event_participants = new ArrayCollection();

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAvatarFile(): ?Files
    {
        return $this->avatar;
    }

    public function setAvatarFile(Files $files): self
    {
        $this->avatar = $files;

        return $this;
    }

    public function getAttributes(): array
    {
        $attr = [
            'id' => $this->getId(),
            'email' => $this->getUsername(),
//            'avatar_path' => $this->getAvatarFile()->getPath(),
        ];

        return $attr;
    }

    /**
     * @return Collection|EventParticipant[]
     */
    public function getEventParticipants(): Collection
    {
        return $this->event_participants;
    }

    public function addEventParticipant(EventParticipant $eventParticipant): self
    {
        if (!$this->event_participants->contains($eventParticipant)) {
            $this->event_participants[] = $eventParticipant;
            $eventParticipant->setUser($this);
        }

        return $this;
    }

    public function removeEventParticipant(EventParticipant $eventParticipant): self
    {
        if ($this->event_participants->contains($eventParticipant)) {
            $this->event_participants->removeElement($eventParticipant);
            // set the owning side to null (unless already changed)
            if ($eventParticipant->getUser() === $this) {
                $eventParticipant->setUser(null);
            }
        }

        return $this;
    }
}
