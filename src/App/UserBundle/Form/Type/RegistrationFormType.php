<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as RegistrationBaseFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends RegistrationBaseFormType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array(
                    'label' => 'form.nom', 
                    'translation_domain' => 'FOSUserBundle'
                )
            )
            ->add('prenom', 'text', array(
                    'label' => 'form.prenom', 
                    'translation_domain' => 'FOSUserBundle'
                )
            )
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
            ->add('profession', 'text', array(
                    'label' => 'form.profession', 
                    'translation_domain' => 'FOSUserBundle', 
                    'required' => false
                )
            )
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
                'constraints' => array( new NotBlank(), new Length( array( 'min' => 6)) )
            ))        
            ->add('roles', 'choice', array(
                'label' => 'form.role', 
                'translation_domain' => 'FOSUserBundle',
                'choices' => array(
                                'client' => 'Client', 
                                'partenaire' => 'Partenaire'
                            ), 
                'mapped' => false, 
                'constraints' => new Choice( array( 
                                    'choices' => array(
                                        'client',
                                        'partenaire'
                                    )
                                )
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'registration';
    }
}
