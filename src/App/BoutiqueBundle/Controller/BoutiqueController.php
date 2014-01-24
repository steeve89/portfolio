<?php

namespace App\BoutiqueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BoutiqueController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBoutiqueBundle:Boutique:index.html.twig');
    }
}
