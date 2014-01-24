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

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Listener responsible to save user connexion log
 */
class UserRegistrationConfirmedListener implements EventSubscriberInterface
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
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRMED => 'onRegistrationConfirmed',
        );
    }
    
    /**
     * onRegistrationConfirmed
     * 
     * Envoie un message à l'administrateur si un nouveau membre est inscrit.
     * 
     * @param \FOS\UserBundle\Event\FilterUserResponseEvent $event
     */
    public function onRegistrationConfirmed( FilterUserResponseEvent $event )
    {        
        //  On recupère l'utilisateur enregistré
        $user = $event->getUser();
        //  On enregistre un log de connexion
        if( $user )
        {   
            //  Récupération du service pour l'envoi du mail
            $mailer = $this->container->get('mailer');
            //  On recupère les informations de contact du webmaster
            $webmaster_name = $this->container->getParameter('webmaster_name');
            $webmaster_email = $this->container->getParameter('webmaster_email');
            //  On définit le message à envoyer au webmaster
            $roles = $user->getRoles();
            $role = ($roles[0] === "ROLE_CLIENT")? "CLIENT":"PARTENAIRE";
            $msg = "Bonjour LOKO Steeve, "."\r\n"."\r\n";
            $msg .= "Un nouveau membre vient de s'inscrire sur le site: "."\r\n";
            $msg .= "Username: ".$user->getUsername()."\r\n";
            $msg .= "Email: ".$user->getEmail()."\r\n";
            $msg .= "Rôle: ".$role."\r\n";
            $msg .= "Date Inscription: ".date('Y-m-d H:i:s')."\r\n"."\r\n";
            $msg .= "Merci."."\r\n"."\r\n";
            $msg .= "Il s'agit d'un message automatique merci de ne pas y repondre.";
            //  Création de l'e-mail : le service mailer utilise SwiftMailer, donc nous créons une instance de Swift_Message
            $message = \Swift_Message::newInstance()
            ->setSubject('NOUVEAU MEMEBRE - LOKO-STEEVE.COM')
            ->setFrom( array( $user->getEmail() => $user->getUsername() ) )
            ->setTo( array( $webmaster_email => $webmaster_name ) )
            ->setBody( nl2br($msg) )
            ->setContentType( 'text/html' );
            // Retour au service mailer, nous utilisons sa méthode « send()» pour envoyer notre $message
            $mailer->send($message);            
        }    
    }
}
