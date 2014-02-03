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
use App\BlogBundle\Entity\Article;

class FixturesArticle extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)	// $manager = EntityManager
    {
        //  Liste des articles
        $articles = array(
            array(
                'Article 1', 'extrait de article extrait de article extrait de article extrait de article', 'fr', 
                'contenu de article 1 contenu de article 1 contenu de article 1'
            ),
            array(
                'Article 2', 'extrait de article extrait de article extrait de article extrait de article', 'fr', 
                'contenu de article 1 contenu de article 1 contenu de article 1'
            ),
            array(
                'Article 3', 'extrait de article extrait de article extrait de article extrait de article', 'fr', 
                'contenu de article 1 contenu de article 1 contenu de article 1'
            ),
            array(
                'Article 4', 'extrait de article extrait de article extrait de article extrait de article', 'fr', 
                'contenu de article 1 contenu de article 1 contenu de article 1'
            )
        );
        //  On crée les articles
        $liste_article = array();
        for ($i = 0; $i <= 3; $i++)
        {
            foreach ($articles as $key => $article) {
                $liste_article[$key] = new Article();
                $liste_article[$key]->setTitre($article[0]);
                $liste_article[$key]->setExtrait($article[1]);
                $liste_article[$key]->setLangue($article[2]);
                $liste_article[$key]->setContenu($article[3]);
                $liste_article[$key]->addCategorie($this->getReference('categorie-'.$i));
                $liste_article[$key]->setUser($this->getReference('super-admin'));
                //  On persiste l'entité
                $em->persist($liste_article[$key]);
                //  On lui ajoute une référence
                $this->addReference('article-'.$i.$key, $liste_article[$key]);
            }
        }
        //  On déclenche l'neregistrement
        $em->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // l'ordre dans lequel les fichiers sont chargés
    }
}
?>
