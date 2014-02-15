<?php

/*
 * This file is part of the PortfolioBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Projet
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\PortfolioBundle\Entity\ProjetRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Projet
{
        /**
    * @ORM\OneToOne(targetEntity="App\PortfolioBundle\Entity\Paiement", mappedBy="projet")
    */
    private $paiement;
    
    /**
    * @ORM\OneToMany(targetEntity="App\PortfolioBundle\Entity\Message", mappedBy="projet", cascade={"persist", "remove"})
    */
    private $messages;
    
    /**
    * @ORM\ManyToOne(targetEntity="App\userBundle\Entity\User", inversedBy="projets")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;
    
    /**
    * @ORM\OneToMany(targetEntity="App\PortfolioBundle\Entity\PieceJointe", mappedBy="projet", cascade={"persist", "remove"})
    */
    private $pieceJointes;
    
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
     * @ORM\Column(name="description", type="text")
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min="50")
     */
    private $description;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="budget", type="integer")
     * 
     * @Assert\NotBlank()
     */
    private $budget;
            
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_butoir", type="date")
     * 
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $dateButoir;
            
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_de_livraison", type="date", nullable=true)
     * 
     * @Assert\Date()
     */
    private $dateDeLivraison;
    
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     * 
     * @Assert\Length(min="5")
     */
    private $url;
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=16)
     */
    private $code;
    
    /**
     * @var string
     * 
     * @ORM\Column(length=128, unique=true)
     * 
     * @Gedmo\Slug(fields={"titre", "code"})
     * 
     */
    private $slug;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_de_creation", type="datetime")
     */
    private $createdDate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_mise_a_jour", type="datetime", nullable=true)
     */
    private $updatedDate;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     * 
     */
    private $status;   
        
    /**
     * @var float
     *
     * @ORM\Column(name="remise", type="float")
     * 
     * @Assert\NotBlank()
     */
    private $remise;
        
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_actived", type="boolean")
     * 
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
     * @return Projet
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
     * Set description
     *
     * @param string $description
     * @return Projet
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set duree
     *
     * @param integer $duree
     * @return Projet
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;
    
        return $this;
    }

    /**
     * Get duree
     *
     * @return integer 
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Projet
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
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->pieceJointes = new ArrayCollection();
        $this->remise = 0;
    }
    
    /**
     * toString
     */
    public function __toString()
    {
        return $this->titre;
    }
    
    /**
     * Add messages
     *
     * @param \App\PortfolioBundle\Entity\Message $messages
     * @return Projet
     */
    public function addMessage(\App\PortfolioBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;
    
        return $this;
    }

    /**
     * Remove messages
     *
     * @param \App\PortfolioBundle\Entity\Message $messages
     */
    public function removeMessage(\App\PortfolioBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set user
     *
     * @param \App\userBundle\Entity\User $user
     * @return Projet
     */
    public function setUser(\App\userBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \App\userBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add pieceJointes
     *
     * @param \App\PortfolioBundle\Entity\PieceJointe $pieceJointes
     * @return Projet
     */
    public function addPieceJointe(\App\PortfolioBundle\Entity\PieceJointe $pieceJointes)
    {
        if (null !== $pieceJointes->file) {
            $pieceJointes->setProjet($this);
            
            $this->pieceJointes[] = $pieceJointes;
        }
    
        return $this;
    }

    /**
     * Remove pieceJointes
     *
     * @param \App\PortfolioBundle\Entity\PieceJointe $pieceJointes
     */
    public function removePieceJointe(\App\PortfolioBundle\Entity\PieceJointe $pieceJointes)
    {
        $this->pieceJointes->removeElement($pieceJointes);
    }

    /**
     * Get pieceJointes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPieceJointes()
    {
        return $this->pieceJointes;
    }
    
    /**
     * Set pieceJointes
     * 
     * @param \App\PortfolioBundle\Entity\ArrayCollection $pieceJointes
     */
    public function setPieceJointes(ArrayCollection $pieceJointes)
    {
        foreach ($pieceJointes as $key => $pieceJointe) {
            $pieceJointe->setProjet($this);
        }
        
        $this->pieceJointes = $pieceJointes;
    }
    
    /**
    * Appeler avant la persistence d'un objet en base de donnée
    * @ORM\PrePersist
    */
    public function onPrePersist()
    {
        $this->setCreatedDate(new \DateTime('now'));
        $this->setIsActived(true);
        $this->setStatus(0);
        $this->setCode(time());
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
     * Set budget
     *
     * @param integer $budget
     * @return Projet
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;
    
        return $this;
    }

    /**
     * Get budget
     *
     * @return integer 
     */
    public function getBudget()
    {
        return $this->budget;
    }
    
    /**
     * Set dateDeLivraison
     *
     * @param \DateTime $dateDeLivraison
     * @return Projet
     */
    public function setDateDeLivraison($dateDeLivraison)
    {
        $this->dateDeLivraison = $dateDeLivraison;
    
        return $this;
    }

    /**
     * Get dateDeLivraison
     *
     * @return \DateTime 
     */
    public function getDateDeLivraison()
    {
        return $this->dateDeLivraison;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Projet
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Projet
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
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     * @return Projet
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
     * Set dateButoir
     *
     * @param \DateTime $dateButoir
     * @return Projet
     */
    public function setDateButoir($dateButoir)
    {
        $this->dateButoir = $dateButoir;
    
        return $this;
    }

    /**
     * Get dateButoir
     *
     * @return \DateTime 
     */
    public function getDateButoir()
    {
        return $this->dateButoir;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Projet
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set paiement
     *
     * @param \App\PortfolioBundle\Entity\Paiement $paiement
     * @return Projet
     */
    public function setPaiement(\App\PortfolioBundle\Entity\Paiement $paiement = null)
    {
        $this->paiement = $paiement;
    
        return $this;
    }

    /**
     * Get paiement
     *
     * @return \App\PortfolioBundle\Entity\Paiement 
     */
    public function getPaiement()
    {
        return $this->paiement;
    }

    /**
     * Set isActived
     *
     * @param boolean $isActived
     * @return Projet
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

    /**
     * Set url
     *
     * @param string $url
     * @return Projet
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set remise
     *
     * @param float $remise
     * @return Projet
     */
    public function setRemise($remise)
    {
        $this->remise = $remise;
    
        return $this;
    }

    /**
     * Get remise
     *
     * @return float 
     */
    public function getRemise()
    {
        return $this->remise;
    }
}