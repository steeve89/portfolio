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
use App\PortfolioBundle\Form\EventListener\PaiementTypeEventSubscriber;

class PaiementType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder    
                ->add('projet', 'entity', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'paiement.field.projet',
                        'class' => 'AppPortfolioBundle:Projet'
                    )
                )
                ->add('prixHorsTaxe', 'text', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'paiement.field.prixHorsTaxe', 
                    )
                )
                ->add('date', 'date', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'paiement.field.date', 
                        'widget' => 'single_text', 
                        'attr' => array('class' => 'datepicker'),
                        'format' => 'dd/MM/yyyy',
                        'required' => false,
                    )
                )
                ->add('status', 'checkbox', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'paiement.field.status', 
                        'required' => false,
                    )
                )
                ->add('details', 'textarea', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'paiement.field.details', 
                        'required' => false,
                    )
                )   
        ;
        //  On ajoute un ecouteur d'évènemet sur ce formulare
        $builder->addEventSubscriber(new PaiementTypeEventSubscriber());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\PortfolioBundle\Entity\Paiement'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'paiement';
    }
}
