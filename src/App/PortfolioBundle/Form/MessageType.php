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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\PortfolioBundle\Form\EventListener\MessageTypeEventSubscriber;
use App\PortfolioBundle\Form\PieceJointeType;

class MessageType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contenu', 'textarea', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'rapport.field.contenu'
                    )
                )      
                ->add('pieceJointe', new PieceJointeType(), array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'rapport.field.pieceJointe',
                    'data_class' => 'App\PortfolioBundle\Entity\PieceJointe',
                    'required' => false
                )
            )  
        ;
        
        //  On ajoute un ecouteur d'évènemet sur ce formulare
        $builder->addEventSubscriber(new MessageTypeEventSubscriber());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\PortfolioBundle\Entity\Message'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'message';
    }
}
