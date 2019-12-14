<?php

declare(strict_types=1);

namespace App\Entity;

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

    public function __construct()
    {
        parent::__construct();

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
            'avatar_path' => $this->getAvatarFile()->getPath(),
        ];

        return $attr;
    }
}
