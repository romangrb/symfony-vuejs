<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlacesRepository")
 */
class Places
{
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
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlaceAttachments", mappedBy="place_id")
     */
    private $placeAttachments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PlaceContents", mappedBy="place_id", cascade={"persist", "remove"})
     */
    private $placeContents;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PlaceLocations", mappedBy="place_id", cascade={"persist", "remove"})
     */
    private $placeLocations;

    public function __construct()
    {
        $this->placeAttachments = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|PlaceAttachments[]
     */
    public function getPlaceAttachments(): Collection
    {
        return $this->placeAttachments;
    }

    public function addPlaceAttachment(PlaceAttachments $placeAttachment): self
    {
        if (! $this->placeAttachments->contains($placeAttachment)) {
            $this->placeAttachments[] = $placeAttachment;
            $placeAttachment->setPlaceId($this);
        }

        return $this;
    }

    public function removePlaceAttachment(PlaceAttachments $placeAttachment): self
    {
        if ($this->placeAttachments->contains($placeAttachment)) {
            $this->placeAttachments->removeElement($placeAttachment);
            // set the owning side to null (unless already changed)
            if ($placeAttachment->getPlaceId() === $this) {
                $placeAttachment->setPlaceId(null);
            }
        }

        return $this;
    }

    public function getPlaceContents(): ?PlaceContents
    {
        return $this->placeContents;
    }

    public function setPlaceContents(PlaceContents $placeContents): self
    {
        $this->placeContents = $placeContents;

        // set the owning side of the relation if necessary
        if ($this !== $placeContents->getPlaceId()) {
            $placeContents->setPlaceId($this);
        }

        return $this;
    }

    public function getPlaceLocations(): ?PlaceLocations
    {
        return $this->placeLocations;
    }

    public function setPlaceLocations(PlaceLocations $placeLocations): self
    {
        $this->placeLocations = $placeLocations;

        // set the owning side of the relation if necessary
        if ($this !== $placeLocations->getPlaceId()) {
            $placeLocations->setPlaceId($this);
        }

        return $this;
    }
}
