<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use App\PortfolioBundle\Event\FilterProjetEvent;
use App\PortfolioBundle\Event\FilterMessageEvent;
use App\UserBundle\Entity\UserLog;

/**
 * Cette classe permet d'écouter certains évènements et d'intéragir avec les utilisateurs
 */
class UserListener
{  
    protected $container;

    /*
     * Constructeur
     */
    public function __construct( Container $container )
    {        
        $this->container = $container;
    }
    
    /**
     * onUserLoginSuccess
     * 
     * Cette function permet de sauvegarder les connexions des utilisateurs sur la plateforme
     * 
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function onUserLoginSuccess( InteractiveLoginEvent $event )
    {        
        //  On recupère l'utilisateur connecté
        $user = $event->getAuthenticationToken()->getUser();
        //  On enregistre un log de connexion
        if( $user )
        {   
            $date = new \DateTime();
            $log = new UserLog();
            $log->setTitre('CONNEXION');
            $log->setContenu("L'utilisateur ".$user->getNom().' '.$user->getPrenom()." s'est connecté le ".$date->format('Y-m-d H:i:s'));
            $log->setDate( $date );
            $log->setUser( $user );

            $em = $this->container->get('doctrine')->getManager();
            $em->persist($log);
            $em->flush();
        }    
    }
    
    /**
     * onRegistrationConfirmed
     * 
     * Envoie un message à l'administrateur si un nouveau membre est inscrit.
     * 
     * @param \FOS\UserBundle\Event\FilterUserResponseEvent $event
     */
    public function onUserRegistrationConfirmed( FilterUserResponseEvent $event )
    {        
        //  On recupère l'utilisateur enregistré
        $user = $event->getUser();
        //  On enregistre un log de connexion
        if( $user )
        {               
            //  Le sujet
            $subject = "NOUVEAU MEMEBRE - LOKO-STEEVE.COM";            
            //  Le destinataire
            $webmaster_name = $this->container->getParameter('webmaster_name');
            $webmaster_email = $this->container->getParameter('webmaster_email');    
            $receiver = array($webmaster_email => $webmaster_name);
            //  L'auteur du mail
            $sender = array($user->getEmail() => $user->getUsername());
            //  On définit le message à envoyer au webmaster
            $roles = $user->getRoles();
            $role = ($roles[0] === "ROLE_CLIENT")? "CLIENT":"PARTENAIRE";
            $msg = "Bonjour LOKO Steeve, "."\r\n"."\r\n";
            $msg .= "Un nouveau membre vient de s'inscrire sur le site: "."\r\n";
            $msg .= "Username: ".$user->getUsername()."\r\n";
            $msg .= "Email: ".$user->getEmail()."\r\n";
            $msg .= "Rôle: ".$role."\r\n";
            $msg .= "Date Inscription: ".date('Y-m-d H:i:s')."\r\n"."\r\n";
            $msg .= "Cordialement,"."\r\n";
            $msg .= "L'équipe."."\r\n"."\r\n";
            $msg .= "Il s'agit d'un message automatique merci de ne pas y repondre.";
            //  On envoit le mail
            $this->sendMail($subject, $msg, $sender, $receiver);
        }    
    }
    
    public function onUserProjetPost( FilterProjetEvent $event )
    {        
        //  On recupère le projet
        $projet = $event->getProjet();
        //  On recupère l'auteur du projet
        $user = $projet->getUser();
        //  On recupère le service router
        $router = $this->container->get('router');
        //  On recupère l'url absolue pour visualiser le projet
        $url_projet = $router->generate('projet_show', array('slug' => $projet->getSlug()), true);        
        //  Le sujet
        $subject = "UN NOUVEAU PROJET EST DISPONIBLE - LOKO-STEEVE.COM";            
        //  Le destinataire
        $webmaster_name = $this->container->getParameter('webmaster_name');
        $webmaster_email = $this->container->getParameter('webmaster_email');    
        $receiver = array($webmaster_email => $webmaster_name);
        //  L'auteur du mail
        $auteur = $user->getNom().' '.$user->getPrenom();
        $sender = array($user->getEmail() => $auteur);
        //  On définit le message à envoyer au webmaster
        $msg = "Bonjour LOKO Steeve, "."\r\n"."\r\n";
        $msg .= "Un nouveau projet est disponible sur le site: "."\r\n";
        $msg .= "Titre du projet: ".$projet->getTitre()."\r\n";
        $msg .= "Auteur: ".$auteur."\r\n";
        $msg .= 'Voir le projet : <a href="' . $url_projet . '">' . $url_projet . '</a>'."\r\n"."\r\n";
        $msg .= "Cordialement,"."\r\n";
        $msg .= "L'équipe."."\r\n"."\r\n";
        $msg .= "Il s'agit d'un message automatique. Merci de ne pas y repondre.";
        //  On envoit le mail
        $this->sendMail($subject, $msg, $sender, $receiver);
    }
    
    public function onUserMessagePost( FilterMessageEvent $event )
    {        
        //  On recupère le projet
        $message = $event->getMessage();
        //  On recupère l'auteur du message
        $user = $message->getUser();
        //  On recupère le projet associé à ce message
        $projet = $message->getProjet();
        $titre_projet = $projet->getTitre();
        //  On recupère le service router
        $router = $this->container->get('router');
        //  On recupère l'url absolue pour visualiser le projet
        $url_rapport_projet = $router->generate('message', array('slug' => $projet->getSlug()), true);        
        //  Le sujet
        $subject = 'UN NOUVEAU MESSAGE POUR LE PROJET "'.$titre_projet.'" - LOKO-STEEVE.COM';            
        //  Les infos de contact de l'administrateur
        $webmaster_name = $this->container->getParameter('webmaster_name');
        $webmaster_email = $this->container->getParameter('webmaster_email');    
        //  Si l'auteur du message est l'admiistrateur, on informe le client.
        //  Si c'est le client l'auteur du message on informe l'administrateur
        $client = $projet->getUser();
        $client_name = $client->getNom().' '.$client->getPrenom();
        $client_email = $client->getEmail();
        if ( $user->getId() !== $client->getId() )
        {
            $receiver = array($client_email => $client_name);
            $sender = array($webmaster_email => $webmaster_name);
            $msg = "Bonjour $client_name, "."\r\n"."\r\n";
        } else {
            $receiver = array($webmaster_email => $webmaster_name);
            $sender = array($client_email => $client_name);
            $msg = "Bonjour LOKO Steeve, "."\r\n"."\r\n";
        }        
        //  On définit le message à envoyer au webmaster
        $msg .= "Un nouveau message est disponible pour vous: "."\r\n";
        $msg .= "Titre du projet: ".$titre_projet."\r\n";
        $msg .= 'Voir le message : <a href="' . $url_rapport_projet . '">' . $url_rapport_projet . '</a>'."\r\n"."\r\n";
        $msg .= "Cordialement,"."\r\n";
        $msg .= "L'équipe."."\r\n"."\r\n";
        $msg .= "Il s'agit d'un message automatique. Merci de ne pas y répondre.";
        //  On envoit le mail
        $this->sendMail($subject, $msg, $sender, $receiver);
    }
    
    public function sendMail( $subject, $body, $sender, $receiver )
    {        
        //  Récupération du service pour l'envoi du mail
        $mailer = $this->container->get('mailer');
        //  On crée le message
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom( $sender )
            ->setTo( $receiver )
            ->setBody( nl2br($body) )
            ->setContentType( 'text/html' );
        // Retour au service mailer, nous utilisons sa méthode « send()» pour envoyer notre $message
        $mailer->send($message); 
    }
}
