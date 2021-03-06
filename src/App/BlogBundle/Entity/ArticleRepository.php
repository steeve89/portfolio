<?php

/*
 * This file is part of the BlogBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
    public function getArticles($nombreParPage, $page, $langue = null)
    {
        //  On vérifie que la page n'est pas inférieur à 1
        if ($page < 1) {
            throw new \InvalidArgumentException('The argument $page can not be less than one ( value : "'.$page.'" ).');
        }
        //  On definie la requête
        $query = $this->createQueryBuilder('a');
        //  Si la langue est définie, on recupère la liste des categories de cette langue
        //  ordonnées selon leur position
        if($langue):
            $query =    $query->where('a.langue = :langue')
                        ->andWhere('a.isActived = 1')
                        ->setParameter('langue', $langue);
        endif;
        //  On recupère la requête
        $query->orderBy('a.id', 'DESC')->getQuery();
        //  On définit l'address à partir duquel commencer la liste
        $query->setFirstResult( ($page-1) * $nombreParPage )
        //  Ainsi que le nombre d'address à afficher
        ->setMaxResults($nombreParPage);
        //  Enfin, on retourne l'objet Paginator correspondant à la requête construite
        return new Paginator($query);
    }
    
    public function getArticleBySlug($slug)
    {        
        //  On definie la requête
        $query = $this->createQueryBuilder('a')
                        ->select('a, c')
                        ->leftJoin('a.commentaires', 'c')
                        ->where('a.slug = :slug')
                        ->andWhere('a.isActived = 1')   
                        ->orWhere('c.isActived = 1')
                        ->setParameter('slug', $slug)
        ;
        //  On retourne le résultat
        return $query->getQuery()->getOneOrNullResult();
    }
    
    public function getArticlesByWord($word)
    {        
        //  On definie la requête
        $query =    $this->createQueryBuilder('a')   
                    ->select('a, c')
                    ->innerJoin('a.categories', 'c')
                    ->where('a.titre LIKE :word')
                    ->andWhere('a.isActived = 1')
                    ->setParameter('word', "%$word%");      
        //  On ordonne suivant la date de création
        $query->orderBy('a.id', 'DESC');
        //  On retourne le résultat
        return $query->getQuery()->getResult();
    }
}
