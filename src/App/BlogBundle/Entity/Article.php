<?php

/*
 * This file is part of the BlogBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\BlogBundle\Entity\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 * 
 * @UniqueEntity("titre")
 */
class Article
{
    /**
    * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="articles")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;
    
    /**
    * @ORM\OneToMany(targetEntity="App\BlogBundle\Entity\Commentaire", mappedBy="article", cascade={"persist", "remove"})
    */
    private $commentaires;
    
    /**
    * @ORM\ManyToMany(targetEntity="App\BlogBundle\Entity\Tag", inversedBy="articles", cascade={"persist"})
    */
    private $tags;    
    
    /**
    * @ORM\ManyToMany(targetEntity="App\BlogBundle\Entity\Categorie", cascade={"persist"})
    * 
     * @Assert\NotBlank()
    */
    private $categories;    
    
    /**
    * @ORM\OneToOne(targetEntity="App\BlogBundle\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
    */
    private $image;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min="50")
     */
    private $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="langue", type="string", length=2)
     * 
     * @Assert\NotBlank()
     */
    private $langue;

    /**
     * @var string
     *
     * @ORM\Column(name="extrait", type="text")
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min="50")
     */
    private $extrait;

    /**
     * @var string
     * 
     * @ORM\Column(length=128, unique=true)
     * 
     * @Gedmo\Slug(fields={"titre"})
     * 
     */
    private $slug;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_de_creation", type="datetime")
     * 
     */
    private $createdDate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_mise_a_jour", type="datetime", nullable=true)
     */
    private $updatedDate;
        
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_actived", type="boolean")
     * 
     * @Assert\NotBlank()
     */
    private $isActived;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Article
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Article
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    
        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set langue
     *
     * @param string $langue
     * @return Article
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;
    
        return $this;
    }

    /**
     * Get langue
     *
     * @return string 
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set extrait
     *
     * @param string $extrait
     * @return Article
     */
    public function setExtrait($extrait)
    {
        $this->extrait = $extrait;
    
        return $this;
    }

    /**
     * Get extrait
     *
     * @return string 
     */
    public function getExtrait()
    {
        return $this->extrait;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->setIsActived(true);
    }
    
    /**
     * toString
     */
    public function __toString()
    {
        return $this->getTitre();
    }
    
    /**
     * Set user
     *
     * @param \App\UserBundle\Entity\User $user
     * @return Article
     */
    public function setUser(\App\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \App\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add commentaires
     *
     * @param \App\BlogBundle\Entity\Commentaire $commentaires
     * @return Article
     */
    public function addCommentaire(\App\BlogBundle\Entity\Commentaire $commentaires)
    {
        $this->commentaires[] = $commentaires;
    
        return $this;
    }

    /**
     * Remove commentaires
     *
     * @param \App\BlogBundle\Entity\Commentaire $commentaires
     */
    public function removeCommentaire(\App\BlogBundle\Entity\Commentaire $commentaires)
    {
        $this->commentaires->removeElement($commentaires);
    }

    /**
     * Get commentaires
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * Add tags
     *
     * @param \App\BlogBundle\Entity\Tag $tags
     * @return Article
     */
    public function addTag(\App\BlogBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    
        return $this;
    }

    /**
     * Remove tags
     *
     * @param \App\BlogBundle\Entity\Tag $tags
     */
    public function removeTag(\App\BlogBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add categories
     *
     * @param \App\BlogBundle\Entity\Categorie $categories
     * @return Article
     */
    public function addCategorie(\App\BlogBundle\Entity\Categorie $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \App\BlogBundle\Entity\Categorie $categories
     */
    public function removeCategorie(\App\BlogBundle\Entity\Categorie $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set image
     *
     * @param \App\BlogBundle\Entity\Image $image
     * @return Article
     */
    public function setImage(\App\BlogBundle\Entity\Image $image)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \App\BlogBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
    * Appeler avant la persistence d'un objet en base de donnée
    * @ORM\PrePersist
    */
    public function onPrePersist()
    {
        $this->setCreatedDate(new \DateTime('now'));
    }

    /**
    * Appeler avant la mise à jour d'un objet en base de donnée
    * @ORM\PreUpdate
    */
    public function onPreUpdate()
    {
        $this->setUpdatedDate(new \DateTime('now'));
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Article
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    
        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     * @return Article
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    
        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime 
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Set isActived
     *
     * @param boolean $isActived
     * @return Article
     */
    public function setIsActived($isActived)
    {
        $this->isActived = $isActived;
    
        return $this;
    }

    /**
     * Get isActived
     *
     * @return boolean 
     */
    public function getIsActived()
    {
        return $this->isActived;
    }
}