<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            //  doctrine/doctrine-fixtures-bundle
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            //  jms/security-extra-bundle
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            //  stof/doctrine-extensions-bundle
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),                 
            //  "braincrafted/bootstrap-bundle
            new Bc\Bundle\BootstrapBundle\BcBootstrapBundle(),        
            //  spraed/pdf-generator-bundle
            new Spraed\PDFGeneratorBundle\SpraedPDFGeneratorBundle(),
            //  craue/formflow-bundle
            //new Craue\FormFlowBundle\CraueFormFlowBundle(),
            //  stfalcon/tinymce-bundle
            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
            //  friendsofsymfony/user-bundle
            new FOS\UserBundle\FOSUserBundle(),
            //  Commande personnalisée Symfony
            new App\GenerateDoctrineCrudBundle\AppGenerateDoctrineCrudBundle(),
            //  Portfolio Bundle
            new App\PortfolioBundle\AppPortfolioBundle(),
            //  Blog Bundle
            new App\BlogBundle\AppBlogBundle(),
            //  Boutique Bundle
            new App\BoutiqueBundle\AppBoutiqueBundle(),
            //  User Bundle
            new App\UserBundle\AppUserBundle(),
            
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
