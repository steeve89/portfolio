<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\UserRepository")
 * 
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User extends BaseUser
{
    /**
    * @ORM\OneToMany(targetEntity="App\UserBundle\Entity\UserLog", mappedBy="user", cascade={"persist"})
    */
    private $userLogs;
    
    /**
    * @ORM\OneToMany(targetEntity="App\PortfolioBundle\Entity\Message", mappedBy="user", cascade={"persist"})
    */
    private $messages;
    
    /**
    * @ORM\OneToMany(targetEntity="App\PortfolioBundle\Entity\Projet", mappedBy="user", cascade={"persist"})
    */
    private $projets;
    
    /**
    * @ORM\OneToMany(targetEntity="App\BlogBundle\Entity\Article", mappedBy="user")
    */
    private $articles;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45)
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=45)
     * 
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     */
    private $prenom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     * 
     * @Assert\Length(min="3")
     */
    private $profession;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * 
     * @Assert\Length(min="100")
     */
    private $description;
    
        /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=15, nullable=true)
     * 
     * @Assert\Length(min="8")
     */
    private $telephone;
    
     /**
     * @var string
     *
     * @ORM\Column(name="skype", type="string", length=255, nullable=true)
     * 
     * @Assert\Length(min="3")
     */
    private $skype;
    
     /**
     * @var float
     *
     * @ORM\Column(name="remise", type="float")
     * 
     * @Assert\NotBlank()
     */
    private $remise;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_inscription", type="datetime")
     */
    private $dateInscription;

    public function __construct()
    {
        parent::__construct();
        $this->dateInscription = new \Datetime();
        $this->remise = 0;
    }

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
     * Set nom
     *
     * @param string $nom
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    
        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set profession
     *
     * @param string $profession
     * @return User
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;
    
        return $this;
    }

    /**
     * Get profession
     *
     * @return string 
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return User
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
     * Set dateInscription
     *
     * @param \DateTime $dateInscription
     * @return User
     */
    public function setDateInscription($dateInscription)
    {
        $this->dateInscription = $dateInscription;
    
        return $this;
    }

    /**
     * Get dateInscription
     *
     * @return \DateTime 
     */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    /**
     * Add userLogs
     *
     * @param \App\UserBundle\Entity\UserLog $userLogs
     * @return User
     */
    public function addUserLog(\App\UserBundle\Entity\UserLog $userLogs)
    {
        $this->userLogs[] = $userLogs;
    
        return $this;
    }

    /**
     * Remove userLogs
     *
     * @param \App\UserBundle\Entity\UserLog $userLogs
     */
    public function removeUserLog(\App\UserBundle\Entity\UserLog $userLogs)
    {
        $this->userLogs->removeElement($userLogs);
    }

    /**
     * Get userLogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserLogs()
    {
        return $this->userLogs;
    }

    /**
     * Add messages
     *
     * @param \App\PortfolioBundle\Entity\Message $messages
     * @return User
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
     * Add projets
     *
     * @param \App\PortfolioBundle\Entity\Projet $projets
     * @return User
     */
    public function addProjet(\App\PortfolioBundle\Entity\Projet $projets)
    {
        $this->projets[] = $projets;
    
        return $this;
    }

    /**
     * Remove projets
     *
     * @param \App\PortfolioBundle\Entity\Projet $projets
     */
    public function removeProjet(\App\PortfolioBundle\Entity\Projet $projets)
    {
        $this->projets->removeElement($projets);
    }

    /**
     * Get projets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjets()
    {
        return $this->projets;
    }

    /**
     * Add articles
     *
     * @param \App\BlogBundle\Entity\Article $articles
     * @return User
     */
    public function addArticle(\App\BlogBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;
    
        return $this;
    }

    /**
     * Remove articles
     *
     * @param \App\BlogBundle\Entity\Article $articles
     */
    public function removeArticle(\App\BlogBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return User
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    
        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set skype
     *
     * @param string $skype
     * @return User
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;
    
        return $this;
    }

    /**
     * Get skype
     *
     * @return string 
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * Set remise
     *
     * @param float $remise
     * @return User
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