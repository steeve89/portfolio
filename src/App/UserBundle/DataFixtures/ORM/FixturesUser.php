<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\UserBundle\Entity\User;

class FixturesUser extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)	// $manager = EntityManager
    {
        //  On crée l'utilisateur SUPER ADMIN steeve
        $super_admn = new User();
        $super_admn->setUsername('admin');
        $super_admn->setNom('LOKO');
        $super_admn->setPrenom('Steeve');
        $super_admn->setProfession('Développeur Web Freelance');
        $super_admn->setEnabled(true);
        $super_admn->setEmail('loko.steeve@yahoo.fr');
        $super_admn->setPlainPassword('admin');
        $super_admn->setRoles(array('ROLE_SUPER_ADMIN'));
        $this->addReference('super-admin', $super_admn);
        //  On crée un partenaire pour les tests
        $partenaire = new User();
        $partenaire->setUsername('partenaire');
        $partenaire->setNom('KOKOYE');
        $partenaire->setPrenom('Eddy');
        $partenaire->setProfession("Développeur Logiciel D'application");
        $partenaire->setEnabled(true);
        $partenaire->setEmail('kodeville@yahoo.fr');
        $partenaire->setPlainPassword('partenaire');
        $partenaire->setRoles(array('ROLE_PARTENAIRE'));
        //  On crée un client pour les tests
        $client = new User();
        $client->setUsername('client');
        $client->setNom('DUBOIS');
        $client->setPrenom('François');
        $client->setEnabled(true);
        $client->setEmail('loko.steeve@gmail.com');
        $client->setPlainPassword('client');
        $client->setRoles(array('ROLE_CLIENT'));
        //  On la persiste
        $em->persist($super_admn);
        $em->persist($partenaire);
        $em->persist($client);
        //  On déclenche l'neregistrement
        $em->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // l'ordre dans lequel les fichiers sont chargés
    }
}
?>
