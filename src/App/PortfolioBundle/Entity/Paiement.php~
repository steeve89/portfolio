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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Paiement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\PortfolioBundle\Entity\PaiementRepository")
 * 
 * @UniqueEntity(fields="projet")
 */
class Paiement
{
    /**
    * @ORM\OneToOne(targetEntity="App\PortfolioBundle\Entity\Projet", inversedBy="paiement")
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
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     * 
     * @Assert\NotBlank()
     */
    private $prixHorsTaxe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     * 
     * @Assert\Datetime()
     */
    private $date;
    
    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text", nullable=true)
     * 
     * @Assert\Length(min="10")
     */
    private $details;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;


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
     * Set prixHorsTaxe
     *
     * @param float $prixHorsTaxe
     * @return Projet
     */
    public function setPrixHorsTaxe($prixHorsTaxe)
    {
        $this->prixHorsTaxe = $prixHorsTaxe;
    
        return $this;
    }

    /**
     * Get prixHorsTaxe
     *
     * @return float 
     */
    public function getPrixHorsTaxe()
    {
        return $this->prixHorsTaxe;
    }


    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Paiement
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Paiement
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set projet
     *
     * @param \App\PortfolioBundle\Entity\Projet $projet
     * @return Paiement
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
     * Set details
     *
     * @param string $details
     * @return Paiement
     */
    public function setDetails($details)
    {
        $this->details = $details;
    
        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }
    
    /**
     * Get Taxe
     * 
     * @return float
     */
    public function getTaxe()
    {
        return $this->getPrixHorsTaxe() * 0.196;
    }
    
    /**
     * Get Taxe
     * 
     * @return float
     */
    public function getRemise()
    {
        return $this->getPrixHorsTaxe() * $this->getProjet()->getRemise();
    }
    
    /**
     * Get Montant TTC de l facture
     * 
     * @return float
     */
    public function getTotal()
    {
        return $this->getPrixHorsTaxe() + $this->getTaxe() - $this->getRemise();
    }
}