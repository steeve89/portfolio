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
use Symfony\Component\Security\Core\SecurityContext;
use App\PortfolioBundle\Form\EventListener\ProjetTypeEventSubscriber;
use App\PortfolioBundle\Form\PieceJointeType;
use Symfony\Component\Validator\Constraints\Choice;

class ProjetType extends AbstractType
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
                ->add('description', 'textarea', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'projet.field.description'
                    )
                )
                ->add('budget', 'choice', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'projet.field.budget', 
                        'choices' => array( 
                            '0' => 'projet.choices.budget.0', '1' => 'projet.choices.budget.1', 
                            '2' => 'projet.choices.budget.2', '3' => 'projet.choices.budget.3', 
                            '4' => 'projet.choices.budget.4' ),
                        'constraints' => new Choice( array( 
                                    'choices' => array(
                                        '0', '1',
                                        '2', '3',
                                        '4'
                                    )
                                )
                            )
                    )
                )
                ->add('dateButoir','date', array(
                        'translation_domain' => 'FOSUserBundle', 
                        'label' => 'projet.field.dateButoir',
                        'widget' => 'single_text', 
                        'attr' => array('class' => 'datepicker'),
                        'format' => 'dd/MM/yyyy'
                    )
                )
                ->add('pieceJointes', 'collection', array(
                        'translation_domain' => 'FOSUserBundle',
                        'type' => new PieceJointeType(), 
                        'label' => 'projet.field.pieceJointe', 
                        'data_class' => null,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                    ) 
                )
        ;
        //  On ajoute un ecouteur d'évènemet sur ce formulare
        $builder->addEventSubscriber(new ProjetTypeEventSubscriber($this->securityContext));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\PortfolioBundle\Entity\Projet'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'projet';
    }
}
