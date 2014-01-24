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

class ArticleTypeEventSubscriber implements EventSubscriberInterface
{    
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $article = $event->getData();
        $form = $event->getForm();
        //  On ajoute l'id de la catégorie en mode edition
        if ($article and null !== $article->getId()) {
            $form->add('identifier', 'hidden', array('data' => $article->getTitre(), 'mapped' => false));
        }  
    }
}