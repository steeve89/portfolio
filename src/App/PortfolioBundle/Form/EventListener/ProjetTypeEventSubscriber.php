<?php

/*
 * This file is part of the PortfolioBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PortfolioBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContext;

class ProjetTypeEventSubscriber implements EventSubscriberInterface
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
        $projet = $event->getData();
        $form = $event->getForm();
        //  On ajoute le titre du projet s'il s'agit d'un nouveau projet
        if (!$projet || null === $projet->getId()) {
            $form->add('titre', 'text', array('translation_domain' => 'FOSUserBundle', 'label' => 'projet.field.titre', ));
        } else {
            $form->add('titre', 'hidden');
        }
        //  S'il s'agit d'un administrateur alors on ajoute de nouveau champs
        if ($this->securityContext->isGranted('ROLE_ADMIN')){
            $form
                ->add('dateDeLivraison', 'date', array('translation_domain' => 'FOSUserBundle', 'label' => 'projet.field.dateDeLivraison', 
                        'widget' => 'single_text', 
                        'attr' => array('class' => 'datepicker'),
                        'format' => 'dd/MM/yyyy'
                    )
                )
                ->add('status', 'choice', array('translation_domain' => 'FOSUserBundle', 'label' => 'projet.field.status', 
                        'choices' => array( 
                            'projet.choices.status.0',  
                            'projet.choices.status.1',
                            'projet.choices.status.2' )
                    )
                );  
        }
        
    }
}