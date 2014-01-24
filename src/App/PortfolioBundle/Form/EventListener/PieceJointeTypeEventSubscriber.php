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

class PieceJointeTypeEventSubscriber implements EventSubscriberInterface
{    
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $piecejointe = $event->getData();
        $form = $event->getForm();
        //  On ajoute le nom de la piece jointe en mode édition
        if ($piecejointe and null !== $piecejointe->getId()) {
            $form->add('identifier', 'hidden', array('data' => $piecejointe->getNom(), 'mapped' => false));
        }  
    }
}