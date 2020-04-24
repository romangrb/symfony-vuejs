<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
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
     * @ORM\OneToMany(targetEntity="App\Entity\EventParticipant", mappedBy="user", orphanRemoval=true)
     */
    private $event_participants;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TemplateVariable", mappedBy="user", orphanRemoval=true)
     */
    private $templateVariables;

    public function __construct()
    {
        parent::__construct();
        $this->event_participants = new ArrayCollection();
        $this->templateVariables = new ArrayCollection();

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAttributes(): array
    {
        $attr = [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail()
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
}
