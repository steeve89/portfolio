<?php

/*
 * This file is part of the PortfolioBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PortfolioBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class ContactHandler 
{
    protected $form;
    protected $request;
    protected $container;
    
    public function __construct(Form $form, Request $request, Container $container)
    {
        $this->form = $form;
        $this->request = $request;
        $this->container = $container;
    }
    
    public function process()
    {
        if( $this->request->getMethod() == 'POST' )
        {
            $this->form->handleRequest($this->request);
            if( $this->form->isValid() )
            {
                $this->onSuccess($this->form->getData());
                return true;
            }
        }
        return false;
    }
    
    public function onSuccess( $data )
    {      
        //  Récupération du service pour l'envoi du mail
        $mailer = $this->container->get('mailer');
        //  On recupère les informations de contact du webmaster
        $webmaster_name = $this->container->getParameter('webmaster_name');
        $webmaster_email = $this->container->getParameter('webmaster_email');
        //  Création de l'e-mail : le service mailer utilise SwiftMailer, donc nous créons une instance de Swift_Message
        $message = \Swift_Message::newInstance()
        ->setSubject($data["objet"])
        ->setFrom( array( $data["email"] => $data["nom"] ) )
        ->setTo( array( $webmaster_email => $webmaster_name ) )
        ->setBody( nl2br($data["message"]).'<br /> Telephone: '.$data["telephone"] )
        ->setContentType( 'text/html' );
        // Retour au service mailer, nous utilisons sa méthode « send()» pour envoyer notre $message
        $mailer->send($message);        
        //  On récupère le service translator et session
        $translator = $this->container->get('translator');
        $session = $this->container->get('session');
        //  On affiche un message à l'utilisateur
        $texteTraduit = $translator->trans('portfolio.contact.form.mail_send');
        $session->getFlashBag()->add('notice', $texteTraduit);
        
        return true;
    }
}

?>