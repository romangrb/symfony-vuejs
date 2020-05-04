<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @Assert\Type("string")
     * @Assert\Length(max = 255)
     * @ORM\Column(type="string", length=255)
     */
    private $username;
    /**
     * @Assert\Type("string")
     * @Assert\Length(max = 255)
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\Type("string")
     * @Assert\Email
     * @Assert\Length(max = 255)
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EventParticipant", mappedBy="user", orphanRemoval=true)
     */
    private $event_participants;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TemplateVariable", mappedBy="user", orphanRemoval=true)
     */
    private $templateVariables;

    /**
     * @ORM\Column(type="json")
     */
    private $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->event_participants = new ArrayCollection();
        $this->templateVariables = new ArrayCollection();
        $this->setRoles(['ROLE_USER']);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function eraseCredentials()
    {

    }

    public function getAttributes(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email
        ];
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

    /**
     * @return Collection|TemplateVariable[]
     */
    public function getTemplateVariables(): Collection
    {
        return $this->templateVariables;
    }

    public function addTemplateVariable(TemplateVariable $templateVariable): self
    {
        if (!$this->templateVariables->contains($templateVariable)) {
            $this->templateVariables[] = $templateVariable;
            $templateVariable->setUser($this);
        }

        return $this;
    }

    public function removeTemplateVariable(TemplateVariable $templateVariable): self
    {
        if ($this->templateVariables->contains($templateVariable)) {
            $this->templateVariables->removeElement($templateVariable);
            // set the owning side to null (unless already changed)
            if ($templateVariable->getUser() === $this) {
                $templateVariable->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Get hash of templates values
     *
     * @return array
     */
    public function getTemplateVariablesHash(): array
    {
        $collection = $this->getTemplateVariables();

        $array = $collection->map(function(TemplateVariable $tv) {
            return [$tv->getTag() => $tv->getValue()];
        })->toArray();

        if (count($array) >! 0) return [];

        $data = call_user_func_array('array_merge', $array);

        return $data;
    }

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }
}