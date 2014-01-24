<?php

namespace App\GenerateDoctrineCrudBundle\Command;

use App\GenerateDoctrineCrudBundle\Generator\DoctrineCrudGenerator;
use App\GenerateDoctrineCrudBundle\Generator\DoctrineFormGenerator;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand as BaseGenerateDoctrineCrudCommand;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;

class GenerateDoctrineCrudCommand extends BaseGenerateDoctrineCrudCommand
{
    protected $generator;
    protected $formGenerator;
    
    protected function configure()
    {
        $this                
        //  les informations de base de doctrine:generate:crud
            ->setDefinition(array(
                new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)'),
                new InputOption('route-prefix', '', InputOption::VALUE_REQUIRED, 'The route prefix'),
                new InputOption('with-write', '', InputOption::VALUE_NONE, 'Whether or not to generate create, new and delete actions'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Use the format for configuration files (php, xml, yml, or annotation)', 'annotation'),
                new InputOption('overwrite', '', InputOption::VALUE_NONE, 'Do not stop the generation if crud controller already exist, thus overwriting all generated files'),                
            ))
        //  Liste des options supllémentaires
            ->addOption( 'form-class', null, InputOption::VALUE_OPTIONAL, 'Quel est la classe par defaut des formulaires <form> ' )
            ->addOption( 'table-class', null, InputOption::VALUE_OPTIONAL, 'Quel est la classe par defaut des tableaux <table> ' )
            ->addOption( 'ul-class', null, InputOption::VALUE_OPTIONAL, 'Quel est la classe par defaut des listes de boutons <ul> ' )
            ->addOption( 'bouton-class', null, InputOption::VALUE_OPTIONAL, 'Quel est la classe par defaut des bouttons <button>' )              
        //  Liste des champs des tableaux
            ->addOption( 'fields-table', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Quelle est la liste des champs qui composent les tableaux index et show ?' )
            ->addOption( 'fields-form', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Quelle est la liste des champs du formulaires avec type et class ?' )
        //  Les informations sur la commandes
            ->setDescription('Generates a CRUD based on a Doctrine entity')
            ->setHelp(<<<EOT
The <info>doctrine:generate:crud</info> command generates a CRUD based on a Doctrine entity.

The default command only generates the list and show actions.

<info>php app/console doctrine:generate:crud:twitter-bootstrap --entity=AcmeBlogBundle:Post --route-prefix=post_admin</info>

Using the --with-write option allows to generate the new, edit and delete actions.

<info>php app/console doctrine:generate:crud:twitter-bootstrap --entity=AcmeBlogBundle:Post --route-prefix=post_admin --with-write</info>

Every generated file is based on a template. There are default templates but they can be overriden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/crud
APP_PATH/Resources/SensioGeneratorBundle/skeleton/crud</info>

And

<info>__bundle_path__/Resources/SensioGeneratorBundle/skeleton/form
__project_root__/app/Resources/SensioGeneratorBundle/skeleton/form</info>

You can check https://github.com/sensio/SensioGeneratorBundle/tree/master/Resources/skeleton
in order to know the file structure of the skeleton
EOT
            )
            ->setName('loko-steeve:crud')
            ->setAliases(array('doctrine:generate:crud:bundle'))
        ;        
    }
    
    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $format = Validators::validateFormat($input->getOption('format'));
        $prefix = $this->getRoutePrefix($input, $entity);
        $withWrite = $input->getOption('with-write');
        $forceOverwrite = $input->getOption('overwrite');
        
    //  On recupère les classes des éléments
        $form_class = $input->getOption('form-class');
        $table_class = $input->getOption('table-class');
        $ul_class = $input->getOption('ul-class');
        $bouton_class = $input->getOption('bouton-class');
    //  On recupère la liste des champs à afficher dans les tableaux
        $fields_table = trim($input->getOption('fields-table'));
        if(empty($fields_table)) $fields_table = $this->getEntityFields($bundle, $entity);
        //$fields_table = $this->removeEndescore($fields_table);
    //  On recupère la liste des champs à afficher dans les formulaires
        $fields_form = trim($input->getOption('fields-form'));
        if(empty($fields_form)) $fields_form = $this->getEntityFields($bundle, $entity);
        //$fields_form = $this->removeEndescore($fields_form);
        // CRUD GENERATOR
        $dialog->writeSection($output, 'CRUD generation');

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        $metadata    = $this->getEntityMetadata($entityClass);
        $bundle      = $this->getContainer()->get('kernel')->getBundle($bundle);
        
    //  On ajoute les classes dans le metadata fieldMappings
        $class = array(
            'form' => $form_class,
            'table' => $table_class,
            'ul' => $ul_class,
            'bouton' => $bouton_class
        );        
    //  On ajoute un attribut table de type boolean pour les champs de l'entité
    //  Si ce champs est à true, on l'affiche dans les tableaux, sinon on le l'affiche pas
        $classmetadata = $this->getTableClassMetaData($metadata[0], $fields_table);
        
        // Generator
        $generator = $this->getGenerator($bundle);
        $generator->generate($bundle, $entity, $classmetadata, $format, $prefix, $withWrite, $forceOverwrite, $class);

        $output->writeln('Generating the CRUD code: <info>OK</info>');

        $errors = array();
        $runner = $dialog->getRunner($output, $errors);

        // form
        if ($withWrite) {
        //  On ajoute un attribut form de type boolean pour les champs de l'entité
        //  Si ce champs est à true, on l'affiche dans le formulaire, sinon on le l'affiche pas
            $formmetadata = $this->getFormClassMetaData($metadata[0], $fields_form);
            $metadata[0] = $formmetadata;
            
            $this->generateForm($bundle, $entity, $metadata);
            $output->writeln('Generating the Form code: <info>OK</info>');
        }

        // routing
        if ('annotation' != $format) {
            $runner($this->updateRouting($dialog, $input, $output, $bundle, $format, $entity, $prefix));
        }

        $dialog->writeGeneratorSummary($output, $errors);
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Doctrine2 CRUD generator');

        // namespace
        $output->writeln(array(
            '',
            'This command helps you generate CRUD controllers and templates.',
            '',
            'First, you need to give the entity for which you want to generate a CRUD.',
            'You can give an entity that does not exist yet and the wizard will help',
            'you defining it.',
            '',
            'You must use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>.',
            '',
        ));

        $entity = $dialog->askAndValidate($output, $dialog->getQuestion('The Entity shortcut name', $input->getOption('entity')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $input->getOption('entity'));
        $input->setOption('entity', $entity);
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        // write?
        $withWrite = $input->getOption('with-write') ?: false;
        $output->writeln(array(
            '',
            'By default, the generator creates two actions: list and show.',
            'You can also ask it to generate "write" actions: new, update, and delete.',
            '',
        ));
        $withWrite = $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to generate the "write" actions', $withWrite ? 'yes' : 'no', '?'), $withWrite);
        $input->setOption('with-write', $withWrite);

        // format
        $format = $input->getOption('format');
        $output->writeln(array(
            '',
            'Determine the format to use for the generated CRUD.',
            '',
        ));
        $format = $dialog->askAndValidate($output, $dialog->getQuestion('Configuration format (yml, xml, php, or annotation)', $format), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateFormat'), false, $format);
        $input->setOption('format', $format);

        // route prefix
        $prefix = $this->getRoutePrefix($input, $entity);
        $output->writeln(array(
            '',
            'Determine the routes prefix (all the routes will be "mounted" under this',
            'prefix: /prefix/, /prefix/new, ...).',
            '',
        ));
        $prefix = $dialog->ask($output, $dialog->getQuestion('Routes prefix', '/'.$prefix), '/'.$prefix);
        $input->setOption('route-prefix', $prefix);
        
        $output->writeln(array(
            '',
            'On definit les classes des elements form, table, ul, a et button.',            
            '',
        ));
        //  Le validateur pour les champs string
        $validator = function ($value) {
            if (is_numeric($value)) {
                throw new \RunTimeException(
                    'Valeur incorrect : chaines de caracteres attendus '
                );
            }
            return $value;
        };
        
        //  form class
        $form_class = $dialog->askAndValidate(
            $output,
            $dialog->getQuestion('Quel est la classe par defaut des formulaires <form> ', false),            
            $validator,
            false,
            '',
            $input->getOption('form-class')
        );
        $input->setOption('form-class', $form_class);
        
        //  table class
        $table_class = $dialog->askAndValidate(
            $output,
            $dialog->getQuestion('Quel est la classe par defaut des tableaux <table> ', false),            
            $validator,
            false,
            '',
            $input->getOption('table-class')
        );
        $input->setOption('table-class', $table_class);
        
        //  ul class
        $ul_class = $dialog->askAndValidate(
            $output,
            $dialog->getQuestion('Quel est la classe par defaut des listes de boutons <ul> ', false),            
            $validator,
            false,
            '',
            $input->getOption('ul-class')
        );
        $input->setOption('ul-class', $ul_class);
        
        //  button  class
        $bouton_class = $dialog->askAndValidate(
            $output,
            $dialog->getQuestion('Quel est la classe par defaut des bouttons <a>, <button> ', false), 
            $validator,
            false,
            '',
            $input->getOption('bouton-class')
        );
        $input->setOption('bouton-class', $bouton_class);
        
        //  Liste des champs de la classe
        $GLOBALS['fields'] = $this->getEntityFields($bundle, $entity);  
        //  Liste des form type valide
        $GLOBALS['form_types'] = $this->getValidFormType();  
        // New section
        $output->writeln(array(
            '',
            'On definit les champs de l\'entite qui seront presents dans les tableaux index et show.',
            '',
            'La liste des champs par defaut est:',
            $GLOBALS['fields'],
            '',
            'Ex: champs1 champs2 (l\'espace entre les champs est obligatoire)',
            '',
        ));              
        //  Validateur fields                
        $fields_and_formType_validator = function ($listes) {            
        //  Si aucune valeur a été entré, on renvoit la liste par defaut
            if(empty($listes)) return $GLOBALS['fields'];
        //  On recupère la liste des champs valides
            $array_fields_valid = explode(' ', $GLOBALS['fields']);
        //  La liste des form type valid
            $array_form_types_valid = $GLOBALS['form_types'];
        //  On recupere la liste sous forme de tableau
            $array_listes = explode(' ', $listes);
            foreach ( $array_listes as $liste ):
            //  S'il y a des ":", on les retire
                $array_liste = explode(':', $liste);
                if (!in_array($array_liste[0], $array_fields_valid)) {
                    throw new \RunTimeException(
                        "'".$array_liste[0].'\' n\'est pas un champs valide.'
                    );
                }
                if(isset($array_liste[1])):
                    if (!in_array($array_liste[1], $array_form_types_valid)) {
                        throw new \RunTimeException(
                            "'".$array_liste[1].'\' n\'est pas un type de formulaire valide.'
                        );
                    }
                endif;
            endforeach;
            
            return $listes;
        };
        
        // fields table        
        $fields_table = $dialog->askAndValidate(
            $output,
            $dialog->getQuestion('Quelle est la liste des champs ', false), 
            $fields_and_formType_validator,
            false,
            '',
            explode(' ', $GLOBALS['fields'])
        );
        $input->setOption('fields-table', $fields_table);
        
        // new section
        $output->writeln(array(
            '',
            'On definit les champs de l\'entite qui seront presents dans le formulaire (formType).',
            '',
            'La liste des champs par defaut est:',
            $this->getEntityFields($bundle, $entity, true),
            '',
            '',
            implode(",", $GLOBALS['form_types']),
            '',
            'Ex: champs1:type1:class1 champs2:type2:class2 (l\'espace entre les champs est obligatoire)',
            '',
        ));   
        
        // fields form                
        $fields_form = $dialog->askAndValidate(
            $output,
            $dialog->getQuestion('Quelle est la liste des champs ', false), 
            $fields_and_formType_validator,
            false,
            '',
            explode(' ', $GLOBALS['fields'])
        );
        $input->setOption('fields-form', $fields_form);
        
        // summary
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf("You are going to generate a CRUD controller for \"<info>%s:%s</info>\"", $bundle, $entity),
            sprintf("using the \"<info>%s</info>\" format.", $format),
            '',
        ));
    }
    /**
     * Permet de définir un nouveau générateur de bundle avec le chemin du skeleton
     * 
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @return Crud\CrudBundle\Generator\DoctrineCrudGenerator
     */
    protected function getGenerator(BundleInterface $bundle = null)
    {
        if (null === $this->generator) {
            $this->generator = new DoctrineCrudGenerator($this->getContainer()->get('filesystem'), __DIR__.'/../Resources/skeleton/crud');
            $this->generator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }
 
        return $this->generator;
    }
    /**
     * Permet de définir un nouveau générateur de formulaire
     * 
     * @param Bundle $bundle
     * @return formGenerator
     */
    protected function getFormGenerator($bundle = null)
    {
        if (null === $this->formGenerator) {
            $this->formGenerator = new DoctrineFormGenerator($this->getContainer()->get('filesystem'));
            $this->formGenerator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->formGenerator;
    }
    
    /**
     * Permet de récupérer les champs d'une classe
     * 
     * @param string $entity
     * @param string $bundle
     * @param boolean $without_primary
     * @return array
     */
    protected function getEntityFields($bundle, $entity, $without_primary_key = false )
    {
    //  On recupere les metadonnees de la classe de l'entité        
        $entityClass            =   $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        $metadata               =   $this->getEntityMetadata($entityClass);
        $classmetadata          =   $metadata[0];
    //  On recupère les champs de la classe
        $fieldNames          =   (array) $classmetadata->fieldNames;
    //  Si la clé primaire n'en fait pas partie, on retire
        if($without_primary_key):
            if (!$classmetadata->isIdentifierNatural()) {
                $fieldNames = array_diff($fieldNames, $classmetadata->identifier);
            }
        endif;
    //  On recupère les champs de la classe sous forme de string
        $fields = '';
        foreach ( $fieldNames as $key => $value ):
            $fields .= $key.' ';
        endforeach;        
    //  On retourne les champs
        return trim($fields);
    }
    
    /**
     * Retourne la classmetadata modifier avec le parametre table ajouter
     * 
     * @param object $metadata
     * @param string $fields
     * @return object
     */
    protected function getTableClassMetaData($metadata, $fields)
    {
    //  On recupère la liste des champs sous forme de tableau
        $fields = explode(' ', $fields);
    //  On recupères la liste des champs de la classe
        $fieldMappings = $metadata->fieldMappings;
    //  On ajoute maintenant le paramètre table
        $clone = $fieldMappings;
        foreach( $fieldMappings as $field => $details ):
        //  On vérifie que le champ est présent dans la liste fournit par l'utilisateur
            if(in_array($field, $fields)):
                $clone[$field]["table"] = true;
            else:
                $clone[$field]["table"] = false;
            endif;
            
        endforeach;
    //  On retourne la classmetadata modifié
        $metadata->fieldMappings = $clone;
        
        return $metadata;
    }
    /**
     *  Retourne la classmetadata modifier avec le parametre form, field_type et field_class ajouter
     * 
     * @param object $metadata
     * @param string $fields
     * @return object
     */
    protected function getFormClassMetaData($metadata, $fields_form)
    {
    //  On recupère la liste des champs sous forme de tableau
        $fields_form = explode(' ', $fields_form);
        $fields = array();
        $fields_type = array();
        $fields_class = array();
        foreach ( $fields_form as $value ):
            $data = explode(':', $value);
            $fields[] = $data[0];
            $fields_type[$data[0]] = (isset($data[1]))? $data[1]: '';
            $fields_class[$data[0]] = (isset($data[2]))? $data[2]: '';
        endforeach;
    //  On recupères la liste des champs de la classe
        $fieldMappings = $metadata->fieldMappings;
    //  On ajoute maintenant le paramètre table
        $clone = $fieldMappings;
        foreach( $fieldMappings as $field => $details ):
        //  On vérifie que le champ est présent dans la liste fournit par l'utilisateur
            if(in_array($field, $fields)):
                $clone[$field]["form"] = true;
                $clone[$field]["fieldType"] = $fields_type[$field];
                $clone[$field]["fieldClass"] = $fields_class[$field];
            else:
                $clone[$field]["form"] = false;
            endif;
            
        endforeach;
    //  On retourne la classmetadata modifié
        $metadata->fieldMappings = $clone;
        
        return $metadata;
    } 
    /**
     * getValidFormType
     * 
     * Retourne la liste des form type valid
     * 
     * @return array
     */
    protected function getValidFormType()
    {
        return array(
            //  Champs de type Texte¶ 
                'text', 'textarea', 'email', 'integer', 'money', 'number', 'password', 'percent', 'search', 'url',
            //  Champs de type Choice
                'choice', 'entity', 'country', 'language', 'locale', 'timezone', 'currency',
            //  Champs de type Date et Time
                'date', 'datetime', 'time', 'birthday',
            //  Autres champs¶
                'checkbox', 'file', 'radio',
            //  Champs de type Groupe
               'collection', 'repeated',
            //  Champs cachés
                'hidden',
            //  Bouttons
                'button', 'reset', 'submit'
        );
    }   
    
    public function removeEndescore($fields) 
    {
        $copy = $fields;
        foreach( $fields as $key => $field )
        {
            $content = explode('_', $field);
            if(count($content) > 0){
                $copy[$key] = $content[0];
                for($i = 1; $i <= count($content); $i++)
                {
                    $copy[$key] .= $content[$i];
                }                
            }
        }
        return $copy;
    }
    
    
} 
?>
