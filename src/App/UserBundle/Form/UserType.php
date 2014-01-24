<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\UserBundle\Form\EventListener\UserTypeEventSubscriber;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class UserType extends AbstractType
{   
    private $type;

    /**
     * @param string $class The User class name
     */
    public function __construct($type)
    {
        $this->type = $type;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder       
                ->add('nom', 'text', array('label' => 'form.nom', 'translation_domain' => 'FOSUserBundle'))
                ->add('prenom', 'text', array('label' => 'form.prenom', 'translation_domain' => 'FOSUserBundle'))                             
                ->add('email', 'email', array( 
                        'label' => 'form.email', 
                        'translation_domain' => 'FOSUserBundle',
                        'constraints' => array( new NotBlank(), new Email())
                    )
                )
                ->add('username', null, array(  
                        'label' => 'form.username', 
                        'translation_domain' => 'FOSUserBundle', 
                        'constraints' => array( new NotBlank(), new Length( array( 'min' => 4)) )
                    )
                )
                ->add('profession', 'text', array('label' => 'form.profession', 'translation_domain' => 'FOSUserBundle', 'required' => false))
                ->add('description', 'textarea', array(
                        'label' => 'form.description', 
                        'translation_domain' => 'FOSUserBundle', 
                        'attr' => array('placeholder' => 'form.placeholder.description'), 
                        'required' => false,
                    )
                )
                ->add('locked', 'checkbox', array('label' => 'form.locked', 'translation_domain' => 'FOSUserBundle', 'required' => false ))
                ->add('telephone', 'text', array('label' => 'form.telephone', 'translation_domain' => 'FOSUserBundle', 'required' => false ))
                ->add('skype', 'text', array('label' => 'form.skype', 'translation_domain' => 'FOSUserBundle', 'required' => false ))
                //  Utilisé pour l'affichage des legends du formulaire client ou partenaire
                ->add('typeUser','hidden', array('data' => $this->type, 'mapped' => false))
        ;
        
        //  On ajoute un ecouteur d'évènemet sur ce formulare
        $builder->addEventSubscriber(new UserTypeEventSubscriber());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }
}
