<?php

/*
 * This file is part of the PortfolioBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\PortfolioBundle\Form\ContactType;
use App\PortfolioBundle\Form\ContactHandler;
use App\PortfolioBundle\Form\ContactClientHandler;
use App\userBundle\Entity\User;
use JMS\SecurityExtraBundle\Annotation\Secure;

class PortfolioController extends Controller
{
    /**
     * Page Accueil du portfolio
     * 
     */
    public function indexAction()
    {
        return $this->render('AppPortfolioBundle:Page:index.html.twig');
    }
    
    /**
     * Page Profile du portfolio
     * 
     */
    public function profileAction()
    {
        return $this->render('AppPortfolioBundle:Page:profil.html.twig');
    }
    
    /**
     * Page Realisations du portfolio
     * 
     */
    public function realisationsAction()
    {
        return $this->render('AppPortfolioBundle:Page:realisations.html.twig');
    }
    
    /**
     * Page Projets du portfolio
     * 
     */
    public function projetsAction()
    {
        return $this->render('AppPortfolioBundle:Page:projets.html.twig');
    }
    
    /**
     * Page comment ça marche Client
     * 
     */
    public function clientAction()
    {
        return $this->render('AppPortfolioBundle:Page:client.html.twig');
    }
    
    /**
     * Page Comment ça marche Partenaire
     * 
     */
    public function partenaireAction()
    {
        return $this->render('AppPortfolioBundle:Page:partenaire.html.twig');
    }
    
    /**
     * Page tarif wordpress
     * 
     */
    public function tarifWordPressAction()
    {
        return $this->render('AppPortfolioBundle:Page:tarif-wordpress.html.twig');
    }

    /**
     * Page Tarif Symfony
     * 
     */
    public function tarifSymfonyAction()
    {
        return $this->render('AppPortfolioBundle:Page:tarif-symfony.html.twig');
    }
    
    /**
     * Page tarif facebook
     * 
     */
    public function tarifFacebookAction()
    {
        return $this->render('AppPortfolioBundle:Page:tarif-facebook.html.twig');
    }
    
    /**
     * Page Contact et Demande de devis
     * 
     */
    public function contactAction()
    {
        //  On définit le formulaire de contact
        $form = $this->createForm(new ContactType());
        //  On définit la classe pour gérer les informations saisies dans le formulaire
        $formHandler = new ContactHandler($form, $this->getRequest(), $this->container);
        //  On exécute le traitement du formulaire. S'il retourne true, alors le formulaire a bien été traité
        if( $formHandler->process() )
        {       
            //  On fait une redirection vers la page d'accueil
            return $this->redirect($this->generateUrl('app_portfolio_homepage'));
        }
        //  On affiche la vue
        return $this->render('AppPortfolioBundle:Page:contact.html.twig', array( 'form' => $form->createView() ));
    }
    
    /**
     * Page service Client
     * 
     * @Secure(roles="ROLE_CLIENT")
     * 
     * Contact Service Client.
     *
     */
    public function serviceClientAction()
    {
        //  On définie le formulaire de contact du service Client
        $form = $this->createForm(new ContactType($this->getUser()));
        //  On définit la classe pour gérer les informations saisies dans le formulaire
        $formHandler = new ContactHandler($form, $this->getRequest(), $this->container);
        //  On exécute le traitement du formulaire. S'il retourne true, alors le formulaire a bien été traité
        if( $formHandler->process() )
        {
            //  On fait une redirection vers la page projet du client
            return $this->redirect($this->generateUrl('projet'));
        }
        // on affiche la vue
        return $this->render('AppPortfolioBundle:Page:service-client.html.twig', array( 'form' => $form->createView() ));
    }
    
    /**
     * Page Contact Client
     * 
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Contact Client.
     *
     */
    public function contactClientAction( User $user )
    {
        //  On définie le formulaire de contact du service Client
        $form = $this->createForm(new ContactType($user));
        //  On définit la classe pour gérer les informations saisies dans le formulaire
        $formHandler = new ContactClientHandler($form, $this->getRequest(), $this->container);
        //  On exécute le traitement du formulaire. S'il retourne true, alors le formulaire a bien été traité
        if( $formHandler->process() )
        {
            //  On fait une redirection vers la page de profile de l'admin
            return $this->redirect($this->generateUrl('fos_user_profile_show'));
        }
        // on affiche la vue
        return $this->render('AppPortfolioBundle:Page:contact-client.html.twig', array( 'form' => $form->createView(), 'user' => $user ));
    }
}
