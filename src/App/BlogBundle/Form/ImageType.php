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
use App\BlogBundle\Form\EventListener\ImageTypeEventSubscriber;

class ImageType extends AbstractType
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
                    'label' => 'image.field.file',
                    'required' => false
                )
            )
            ->add('alt', 'text', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'image.field.alt',
                    'required' => false
                )
            )        
        ;
        //  On ajoute un ecouteur d'évènemet sur ce formulare
        $builder->addEventSubscriber(new ImageTypeEventSubscriber());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\BlogBundle\Entity\Image'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'image';
    }
}
