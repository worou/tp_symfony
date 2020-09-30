<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @Vich\Uploadable
 * 
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank(message="Champ obligatoire")
     * @Assert\Length(
     *      min = 4,
     *      max = 6,
     *      minMessage = "Le titre doit avoir au moins {{ limit }} caractères",
     *      maxMessage = "Le titre doit avoir au plus {{ limit }} caractères"
     *   )
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=30)
     * 
     * @Assert\NotNull
     *
     */
    private $auteur;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $parution;

    /**
     * @ORM\Column(type="string", length=100)
     *
     */
    private $image;

    /**
     * @ORM\Column(type="text")
     * 
     * @Assert\NotNull
     *
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="article", orphanRemoval=true)
     */
    private $commentaires;

    /**
     * @Vich\UploadableField(mapping="article_image", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getParution(): ?\DateTimeInterface
    {
        return $this->parution;
    }

    public function setParution(?\DateTimeInterface $parution): self
    {
        $this->parution = $parution;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setArticle($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getArticle() === $this) {
                $commentaire->setArticle(null);
            }
        }

        return $this;
    }

    public function setImageFile(File $imageFile = null):self
    {
        $this->imageFile = $imageFile;
        
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($imageFile) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }
}
