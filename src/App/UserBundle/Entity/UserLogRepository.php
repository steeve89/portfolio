<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * UserLogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserLogRepository extends EntityRepository
{
    public function getUserLogs($nombreParPage, $page, $user = null)
    {
        //  On vérifie que la page n'est pas inférieur à 1
        if ($page < 1) {
        throw new \InvalidArgumentException('The argument $page can not be less than one ( value : "'.$page.'" ).');
        }
        //  On recupère la requête
        $query = $this->createQueryBuilder('a')->join('a.user', 'u');
        if($user):
            $query =    $query->where('a.user = :user')
                        ->setParameter('user', $user);
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
}