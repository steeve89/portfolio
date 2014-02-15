<?php

/*
 * This file is part of the BlogBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\BlogBundle\Form\EventListener\CategorieTypeEventSubscriber;

class CategorieType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder     
            ->add('langue', 'locale', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'categorie.field.langue', 
                )
            )
            ->add('nom', 'text', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'categorie.field.nom', 
                )
            )
            ->add('description', 'textarea', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'categorie.field.description', 
                )
            )
            ->add('position', 'text', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'categorie.field.position', 
                )
            )            
            ->add('isActived', 'checkbox', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'categorie.field.isActived', 
                    'required' => false
                )
            )
        ;
        //  On ajoute un ecouteur d'évènemet sur ce formulare
        $builder->addEventSubscriber(new CategorieTypeEventSubscriber());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\BlogBundle\Entity\Categorie'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'categorie';
    }
}
