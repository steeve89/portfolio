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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\PortfolioBundle\Entity\MessageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Message
{
    /**
    * @ORM\OneToOne(targetEntity="App\PortfolioBundle\Entity\PieceJointe", inversedBy="message", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
    */
    private $pieceJointe;
    
    /**
    * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="messages")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;
    
    /**
    * @ORM\ManyToOne(targetEntity="App\PortfolioBundle\Entity\Projet", inversedBy="messages")
    * @ORM\JoinColumn(nullable=false)
    */
    private $projet;
    
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
     * @ORM\Column(name="contenu", type="text")
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min="20")
     */
    private $contenu;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Message
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
     * Set user
     *
     * @param \App\UserBundle\Entity\User $user
     * @return Message
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
     * Set projet
     *
     * @param \App\PortfolioBundle\Entity\Projet $projet
     * @return Message
     */
    public function setProjet(\App\PortfolioBundle\Entity\Projet $projet)
    {
        $this->projet = $projet;
    
        return $this;
    }

    /**
     * Get projet
     *
     * @return \App\PortfolioBundle\Entity\Projet 
     */
    public function getProjet()
    {
        return $this->projet;
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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Message
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
     * @return Message
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
     * Set pieceJointe
     *
     * @param \App\PortfolioBundle\Entity\PieceJointe $pieceJointe
     * @return Message
     */
    public function setPieceJointe(\App\PortfolioBundle\Entity\PieceJointe $pieceJointe = null)
    {
        $this->pieceJointe = $pieceJointe;
    
        return $this;
    }

    /**
     * Get pieceJointe
     *
     * @return \App\PortfolioBundle\Entity\PieceJointe 
     */
    public function getPieceJointe()
    {
        return $this->pieceJointe;
    }
}