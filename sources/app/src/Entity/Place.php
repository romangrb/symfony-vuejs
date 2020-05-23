<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlaceRepository")
 * @ORM\Table(name="places")
 */
class Place
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlaceAttachment", mappedBy="place", cascade={"persist", "remove"})
     */
    private $attachments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PlaceContent", mappedBy="place", cascade={"persist", "remove"})
     */
    private $placeContent;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PlaceLocation", mappedBy="place", cascade={"persist", "remove"})
     */
    private $placeLocation;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|PlaceAttachment[]
     */
    public function getAttachments(): ?Collection
    {
        return $this->attachments;
    }

    public function addAttachment(PlaceAttachment $placeAttachment): self
    {
        if (! $this->attachments->contains($placeAttachment)) {
            $this->attachments[] = $placeAttachment;
            $placeAttachment->setPlace($this);
        }

        return $this;
    }

    public function removeAttachment(PlaceAttachment $placeAttachment): self
    {
        if ($this->attachments->contains($placeAttachment)) {
            $this->attachments->removeElement($placeAttachment);
            // set the owning side to null (unless already changed)
            if ($placeAttachment->getPlace() === $this) {
                $placeAttachment->setPlace(null);
            }
        }

        return $this;
    }

    public function getPlaceContent(): ?PlaceContent
    {
        return $this->placeContent;
    }

    public function setPlaceContent(?PlaceContent $placeContent): self
    {
        $this->placeContent = $placeContent;

        // set the owning side of the relation if necessary
        $newPlaceContent = $placeContent === null ? null : $this;
        if ($newPlaceContent !== $placeContent->getPlace()) {
            $placeContent->setPlace($this);
        }

        return $this;
    }

    public function getPlaceLocation(): ?PlaceLocation
    {
        return $this->placeLocation;
    }

    public function setPlaceLocation(?PlaceLocation $placeLocation): self
    {
        $this->placeLocation = $placeLocation;

        // set the owning side of the relation if necessary
        $newPlaceLocation = $placeLocation === null ? null : $this;
        if ($newPlaceLocation !== $placeLocation->getPlace()) {
            $placeLocation->setPlace($this);
        }

        return $this;
    }
}
