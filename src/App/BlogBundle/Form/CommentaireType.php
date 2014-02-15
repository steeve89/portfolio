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
use Symfony\Component\Security\Core\SecurityContext;
use App\BlogBundle\Form\EventListener\CommentaireTypeEventSubscriber;
use Symfony\Component\Validator\Constraints\Choice;

class CommentaireType extends AbstractType
{
    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', 'choice', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'commentaire.field.note', 
                    'data' => '5',
                    'choices' => array( 
                        '0' => '0', '1' => '1', 
                        '2' => '2', '3'=> '3' , 
                        '4' => '4', '5' => '5' 
                    ),
                    'constraints' => new Choice( array( 
                                    'choices' => array(
                                        '0', '1',
                                        '2', '3',
                                        '4', '5'
                                    )
                                )
                            )
                )
            )
            ->add('contenu', 'textarea', array(
                    'translation_domain' => 'FOSUserBundle', 
                    'label' => 'commentaire.field.contenu'
                )
            )              
        ;
        //  On ajoute un ecouteur d'évènemet sur ce formulare
        $builder->addEventSubscriber(new CommentaireTypeEventSubscriber($this->securityContext));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\BlogBundle\Entity\Commentaire'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'commentaire';
    }
}
