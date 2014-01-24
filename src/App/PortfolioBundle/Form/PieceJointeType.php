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
use App\PortfolioBundle\Form\EventListener\PieceJointeTypeEventSubscriber;

class PieceJointeType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('file', 'file', array( 
                    'translation_domain' => 'FOSUserBundle',
                    'label' => 'pieceJointe.field.file',
                    'required' => false
                ))        
        ;
        
        //  On ajoute un ecouteur d'évènemet sur ce formulare
        $builder->addEventSubscriber(new PieceJointeTypeEventSubscriber());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\PortfolioBundle\Entity\PieceJointe'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'piecejointe';
    }
}
