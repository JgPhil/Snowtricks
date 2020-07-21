<?php

namespace App\Entity;

use App\Entity\Figure;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 */
class Picture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("figure_read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"figure_read", "user_read"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"figure_read", "user_read"})
     */
    private $figure_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity=Figure::class, inversedBy="pictures")
     */
    private $figure;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pictures")
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"figure_read", "user_read"})
     */
    private $sortOrder;

    

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

    public function getFigureId(): ?int
    {
        return $this->figure_id;
    }

    public function setFigureId(?int $figure_id): self
    {
        $this->figure_id = $figure_id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getFigure(): ?Figure
    {
        return $this->figure;
    }

    public function setFigure(?Figure $figure): self
    {
        $this->figure = $figure;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

}
