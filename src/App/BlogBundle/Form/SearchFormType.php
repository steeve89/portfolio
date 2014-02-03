<?php

namespace App\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('search', 'text', array(
                'label' => 'blog.searchform.field.search',
                'attr' => array( 
                    'class' => 'span10 search-query', 
                    'placeholder' => 'blog.searchform.placeholder'
                ),
                'constraints' => new NotBlank()
            )
        );
    }

    public function getName()
    {
        return 'searchform';
    }
}
?>
