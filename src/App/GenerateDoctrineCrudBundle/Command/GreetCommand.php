<?php

namespace App\GenerateDoctrineCrudBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;

class GreetCommand extends Command
{
     protected $choices;
    
    protected function configure()
    {
        $this
            ->setName('demo:greet')
            ->setDescription('Saluez quelqu\'un')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Qui voulez vous saluez?'
            )            
            ->addOption(
               'yell',
               null,
               InputOption::VALUE_NONE,
               'Si défini, la réponse est affichée en majuscules'
            )       
            ->addOption(
                'liste',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Qui voulez vous saluez (séparer les noms par des espaces)?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Salut, '.$name;
        } else {
            $text = 'Salut';
        }
        
        $liste = $input->getOption('liste');
        if($liste):            
            $text .= ' '.$liste;            
        endif;
        
        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $output->writeln($text);
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        
        $validator = function ($name) {
            if (is_numeric($name)) {
                throw new \RunTimeException(
                    'Valeur incorrect : chaines de caracteres attendus '
                );
            }
            return $name;
        };
        $GLOBALS['choices'] = array('a', 'b', 'c');
        $validator_array = function ($liste) {             
            $array_liste = explode(' ', $liste);
            foreach ( $array_liste as $value ):
                if (!in_array($value, $GLOBALS['choices'])) {
                    throw new \RunTimeException(
                        $value.' n\'est pas valide'
                    );
                }
            endforeach;
            
            return $liste;
        };
        $output->writeln(' Choississez un élément de la liste ');
        $liste = $dialog->askAndValidate(
            $output,
            'choississez votre liste (a, b, c): ',
            $validator_array,
            false,
            '',
            array('a', 'b') 
        );        
        $input->setOption('liste', $liste);
        
        $name = $dialog->askAndValidate(
            $output,
            'Qui voulez vous saluez? ',
            $validator,
            false,
            '',
            $input->getArgument('name')
        );        
        $input->setArgument('name', $name);    
        
        $yell = $dialog->ask(
            $output,
            'Si defini, la reponse est affichee en majuscules? ',
            $input->getOption('yell')          
        );                       
        $input->setOption('yell', $yell);
    }
}

?>
