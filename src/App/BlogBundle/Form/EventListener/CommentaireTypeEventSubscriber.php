<?php

/*
 * This file is part of the PortfolioBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\BlogBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContext;

class CommentaireTypeEventSubscriber implements EventSubscriberInterface
{
    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }
    
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $commentaire = $event->getData();
        $form = $event->getForm();
        //  On ajoute le titre du projet s'il s'agit d'un nouveau projet
        if ($commentaire and null !== $commentaire->getId()) {
            $form->add('identifier', 'hidden', array('data' => $commentaire->getId(), 'mapped' => false));
        }
        //Si l'utilisateur est un epersonne anonyme, on demande ses informations personnelles
        if( !$this->securityContext->isGranted('ROLE_CLIENT') )
        {
            $form
                ->add('nom', 'text', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'commentaire.field.nom', 
                    )
                )
                ->add('prenom', 'text', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'commentaire.field.prenom', 
                    )
                )
                ->add('email', 'email', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'commentaire.field.email', 
                    )
                );
        }
        //  S'il s'agit d'un administrateur alors on ajoute de nouveau champs
        if ($this->securityContext->isGranted('ROLE_ADMIN')){
            $form
                ->add('isActived', 'checkbox', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'commentaire.field.isActived', 
                        'required' => false
                    )
                )
                ;  
        }
        
    }
}