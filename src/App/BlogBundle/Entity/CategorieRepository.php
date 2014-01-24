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
 * CategorieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategorieRepository extends EntityRepository
{
    public function getCategories($nombreParPage, $page, $langue = null)
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
                        ->orderBy('a.position', 'ASC')
                        ->setParameter('langue', $langue);
        else:
            $query->orderBy('a.id', 'DESC');
        endif;
        //  On recupère la requête
        $query->getQuery();
        //  On définit l'address à partir duquel commencer la liste
        $query->setFirstResult( ($page-1) * $nombreParPage )
        //  Ainsi que le nombre d'address à afficher
        ->setMaxResults($nombreParPage);
        //  Enfin, on retourne l'objet Paginator correspondant à la requête construite
        return new Paginator($query);
    }
}
