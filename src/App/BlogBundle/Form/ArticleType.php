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
use App\BlogBundle\Form\ImageType;
use App\BlogBundle\Form\EventListener\ArticleTypeEventSubscriber;

class ArticleType extends AbstractType
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
                    'label' => 'article.field.langue', 
                )
            )
            ->add('titre', 'text', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'article.field.titre', 
                )
            )
            ->add('extrait', 'textarea', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'article.field.extrait', 
                )
            )
            ->add('contenu', 'textarea', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'article.field.contenu', 
                    'attr' => array(
                        'class' => 'tinymce',
                        'data-theme' => 'advanced',
                    ),
                    'required' => false
                )
            )
            ->add('isActived', 'checkbox', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'article.field.isActived', 
                    'required' => false
                )
            )            
            ->add('categories', 'entity', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'article.field.categories',
                    'class' => 'App\BlogBundle\Entity\Categorie',
                    'expanded' => false,
                    'multiple' => true
                )
            )
            ->add('image', new ImageType(), array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'article.field.image',
                    'data_class' => 'App\BlogBundle\Entity\Image',
                    'required' => false
                )
            )                    
        ;
        //  On ajoute un ecouteur d'évènemet sur ce formulare
        $builder->addEventSubscriber(new ArticleTypeEventSubscriber());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\BlogBundle\Entity\Article'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'article';
    }
}
