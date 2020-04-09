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

    private $placeAttachment;

    private $placeContent;

    private $placeLocation;

    public function __construct()
    {
        $this->placeAttachment = new ArrayCollection();
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|PlaceAttachment[]
     */
    public function getPlaceAttachment(): ?Collection
    {
        return $this->placeAttachment;
    }

    public function addPlaceAttachment(PlaceAttachment $placeAttachment): self
    {
        if (! $this->placeAttachment->contains($placeAttachment)) {
            $this->placeAttachment[] = $placeAttachment;
            $placeAttachment->setPlace($this);
        }

        return $this;
    }

    public function removePlaceAttachment(PlaceAttachment $placeAttachment): self
    {
        if ($this->placeAttachment->contains($placeAttachment)) {
            $this->placeAttachment->removeElement($placeAttachment);
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

    public function setPlaceContent(PlaceContent $placeContent): self
    {
        $this->placeContent = $placeContent;

        // set the owning side of the relation if necessary
        if ($this !== $placeContent->getPlace()) {
            $placeContent->setPlace($this);
        }

        return $this;
    }

    public function getPlaceLocation(): ?PlaceLocation
    {
        return $this->placeLocation;
    }

    public function setPlaceLocations(PlaceLocation $placeLocation): self
    {
        $this->placeLocation = $placeLocation;

        // set the owning side of the relation if necessary
        if ($this !== $placeLocation->getPlace()) {
            $placeLocation->setPlace($this);
        }

        return $this;
    }
}
