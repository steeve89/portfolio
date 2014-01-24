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
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Collection;

class ContactType extends AbstractType
{    
    private $user;
    
    /**
     * Constructor
     * 
     */
    public function __construct( $user = null) 
    {
        $this->user = $user;
    }
    
    /**
     * buildForm
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {           
        //  On recupère des informations sur l'utilisateur s'l est connecté
        $user = $this->user;
        $nom = ($user) ? $user->getNom() : '';
        $prenom = ($user) ? $user->getPrenom() : '';
        $email = ($user) ? $user->getEmail() : '';
        //  On créer le formulaire
        $builder->add( 'nom', 'text', array( 'label' => 'portfolio.contact.form.field.nom_prenom', 'data' => $prenom . ' ' . $nom) )
                ->add( 'email', 'email', array( 'label' => 'portfolio.contact.form.field.email', 'data' => $email) )
                ->add( 'telephone', 'text', array( 'label' => 'portfolio.contact.form.field.telephone', 'required' => false ) )
                ->add( 'objet', 'text', array( 'label' => 'portfolio.contact.form.field.objet') )                
                ->add( 'message', 'textarea', array( 'label' => 'portfolio.contact.form.field.message', 'attr' => array( 'rows' => 8) ) );
    }
    
    /**
     * setDefaultOptions
     * 
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        //  On définie ici les contraintes pour notre formulaire
        $collectionConstraint = new Collection(array(            
            'nom' => array( new NotBlank(), new Length( array('min' => 3)) ),            
            'email' => array( new NotBlank(), new Email() ),            
            'telephone' => new Length( array('min' => 8) ),
            'objet' => array( new NotBlank(), new Length( array('min' => 5)) ),            
            'message' => array( new NotBlank(), new Length( array('min' => 10)) ),
        ));

        $resolver->setDefaults(array(
            'constraints' => $collectionConstraint
        ));
    }
    
    /**
     * getName
     * 
     * @return string
     */
    public function getName()
    {
        return 'contact';
    }
}

?>
