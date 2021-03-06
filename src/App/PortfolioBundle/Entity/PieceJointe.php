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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PieceJointe
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\PortfolioBundle\Entity\PieceJointeRepository")
 * 
 * @ORM\HasLifecycleCallbacks
 */
class PieceJointe
{
    /**
    * @ORM\OneToOne(targetEntity="App\PortfolioBundle\Entity\Message", mappedBy="pieceJointe")
    */
    private $message;
    
    /**
    * @ORM\ManyToOne(targetEntity="App\PortfolioBundle\Entity\Projet", inversedBy="pieceJointes")
    * @ORM\JoinColumn(nullable=true)
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
     * @ORM\Column(name="nom", type="string", length=255)
     * 
     * @Assert\NotBlank()
     */
    private $nom;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

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
     * @return PieceJointe
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
     * Set projet
     *
     * @param \App\PortfolioBundle\Entity\Projet $projet
     * @return PieceJointe
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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // On vérifie que la pièce jointe n'est pas lié à un message            
            $message = $this->getMessage();
            if( !$message ){
                $this->nom = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
            } else {
                //  On précise qu'il s'agit d'un rapport
                $this->nom = 'rapport-'.sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
            }            
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // s'il y a une erreur lors du déplacement du fichier, une exception
        // va automatiquement être lancée par la méthode move(). Cela va empêcher
        // proprement l'entité d'être persistée dans la base de données si
        // erreur il y a
        $this->file->move($this->getUploadRootDir(), $this->nom);

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }
    
    public function getAbsolutePath()
    {
        return null === $this->nom ? null : $this->getUploadRootDir().'/'.$this->nom;
    }

    public function getWebPath()
    {
        return null === $this->nom ? null : $this->getUploadDir().'/'.$this->nom;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/piecejointe';
    }

    /**
     * Set message
     *
     * @param \App\PortfolioBundle\Entity\Message $message
     * @return PieceJointe
     */
    public function setMessage(\App\PortfolioBundle\Entity\Message $message = null)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return \App\PortfolioBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }
}