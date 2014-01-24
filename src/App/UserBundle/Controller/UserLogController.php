<?php

/*
 * This file is part of the UserBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * UserLog controller.
 *
 * @Route("/userlog")
 */
class UserLogController extends Controller
{

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Lists all UserLog entities.
     *
     * @Route("/", name="userlog")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        //  On recupère toutes les entités
        $nombreParPage = $this->container->getParameter('userlog');         
        $entities =   $em->getRepository('AppUserBundle:UserLog')->getUserLogs($nombreParPage, $page);
        //  Le nombre de page à afficher
        $nb_pages = (count($entities) > 0) ? ceil(count($entities)/$nombreParPage) : 1;
        //  On affiche la vue
        return array(
            'entities' => $entities,
            'page' => $page,
            'nb_pages' => $nb_pages,
        );
    }
}
