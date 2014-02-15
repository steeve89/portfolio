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
        //  Le contenu par defaut des articles
        $article = array(
            'Article', 'extrait de article extrait de article extrait de article extrait de article', 'fr', 
            'contenu de article 1 contenu de article 1 contenu de article 1'            
        );
        //  On crée les articles
        $liste_article = array();
        $s = 1;
        for ($i = 0; $i <= 3; $i++)
        {
            for ($j = 1; $j <= 4; $j++)
            {
                $liste_article[$s] = new Article();
                $liste_article[$s]->setTitre($article[0].' '. ($s));
                $liste_article[$s]->setExtrait($article[1]);
                $liste_article[$s]->setLangue($article[2]);
                $liste_article[$s]->setContenu($article[3]);
                $liste_article[$s]->addCategorie($this->getReference('categorie-'.$i));
                $liste_article[$s]->setUser($this->getReference('super-admin'));
                //  On persiste l'entité
                $em->persist($liste_article[$s]);
                //  On lui ajoute une référence
                $this->addReference('article-'.$s, $liste_article[$s]);
                $s++;
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
