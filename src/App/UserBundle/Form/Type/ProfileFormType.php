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

use FOS\UserBundle\Form\Type\ProfileFormType as ProfileBaseFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword as OldUserPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileFormType extends ProfileBaseFormType
{
    private $class;
    private $security_context;
     
    /**
     * @param string $class The User class name
     */
    public function __construct(SecurityContext $security_context, $class)
    {
        $this->class = $class;
        $this->security_context = $security_context;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (class_exists('Symfony\Component\Security\Core\Validator\Constraints\UserPassword')) {
            $constraint = new UserPassword();
        } else {
            // Symfony 2.1 support with the old constraint class
            $constraint = new OldUserPassword();
        }
        
        //  Le changement du username et de l'adresse email n'est pas autorisé pour le moment
        //$this->buildUserForm($builder, $options);

        $builder
            ->add('nom', 'text', array('label' => 'form.nom', 'translation_domain' => 'FOSUserBundle'))
            ->add('prenom', 'text', array('label' => 'form.prenom', 'translation_domain' => 'FOSUserBundle'))
            ->add('profession', 'text', array('label' => 'form.profession', 'translation_domain' => 'FOSUserBundle', 'constraints' => array( new NotBlank() ) ))
            ->add('description', 'textarea', array('label' => 'form.description', 'translation_domain' => 'FOSUserBundle', 'attr' => array('placeholder' => 'form.placeholder.description'), 'constraints' => array( new NotBlank()) ))
            ->add('telephone', 'text', array('label' => 'form.telephone', 'translation_domain' => 'FOSUserBundle', 'required' => false ))
            ->add('skype', 'text', array('label' => 'form.skype', 'translation_domain' => 'FOSUserBundle', 'required' => false ))
            ->add('current_password', 'password', array(
            'label' => 'form.current_password',
            'translation_domain' => 'FOSUserBundle',
            'mapped' => false,
            'constraints' => $constraint,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'profile',
        ));
    }

    public function getName()
    {
        return 'profile';
    }

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
        ;
    }
}
