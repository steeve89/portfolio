<?php

namespace App\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBlogBundle:Blog:index.html.twig');
    }
}
