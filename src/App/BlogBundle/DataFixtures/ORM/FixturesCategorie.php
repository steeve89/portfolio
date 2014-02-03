<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\BlogBundle\Entity\Categorie;

class FixturesCategorie extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)	// $manager = EntityManager
    {
        //  Liste des categories
        $categories = array(
            array(
                'WordPress', 'description de WordPress', 'fr', '1'
            ),
            array(
                'Symfony', 'description de Symfony', 'fr', '2'
            ),
            array(
                'Facebook', 'description de Facebook', 'fr', '3'
            ),
            array(
                'Divers', 'description de divers', 'fr', '4'
            )
        );
        //  On crée les Categories
        $liste_categorie = array();
        foreach ($categories as $key => $categorie) {
            $liste_categorie[$key] = new Categorie();
            $liste_categorie[$key]->setNom($categorie[0]);
            $liste_categorie[$key]->setDescription($categorie[1]);
            $liste_categorie[$key]->setLangue($categorie[2]);
            $liste_categorie[$key]->setPosition($categorie[3]);
            //  On persiste l'entité
            $em->persist($liste_categorie[$key]);
            //  On lui ajoute une référence
            $this->addReference('categorie-'.$key, $liste_categorie[$key]);
        }
        //  On déclenche l'neregistrement
        $em->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // l'ordre dans lequel les fichiers sont chargés
    }
}
?>
