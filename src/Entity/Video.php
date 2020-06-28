<?php

namespace App\Entity;

use App\Entity\Figure;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VideoRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 */
class Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("figure_read")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups("figure_read")
     */
    private $figure_id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("figure_read")
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=Figure::class, inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $figure;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFigureId(): ?int
    {
        return $this->figure_id;
    }

    public function setFigureId(int $figure_id): self
    {
        $this->figure_id = $figure_id;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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
}
