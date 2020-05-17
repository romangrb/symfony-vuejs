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
     * @ORM\Column(type="text", nullable=true)
     */
    private $html;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $css;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlaceAttachment", mappedBy="place", cascade={"persist", "remove"})
     */
    private $placeAttachment;

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

    /**
     * @return string|null
     */
    public function getHTML(): ?string
    {
        return $this->html;
    }

    /**
     * @return string|null
     */
    public function getCSS(): ?string
    {
        return $this->css;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string|null $html
     * @return $this
     */
    public function setHTML(?string $html): self
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @param string|null $css
     * @return $this
     */
    public function setCSS(?string $css): self
    {
        $this->css = $css;

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
