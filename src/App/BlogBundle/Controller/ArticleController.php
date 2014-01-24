<?php

/*
 * This file is part of the BlogBundle.
 *
 * (c) LOKO Steeve <loko.steeve@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\BlogBundle\Entity\Article;
use App\BlogBundle\Form\ArticleType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Article controller.
 *
 * @Route("/article")
 */
class ArticleController extends Controller
{

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Lists all Article entities.
     *
     * @Route("/", name="article")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        //  On recupère toutes les entités
        $nombreParPage = $this->container->getParameter('article');         
        $articles =   $em->getRepository('AppBlogBundle:Article')->getArticles($nombreParPage, $page);
        //  Le nombre de page à afficher
        $nb_pages = (count($articles) > 0) ? ceil(count($articles)/$nombreParPage) : 1;
        //  On affiche la vue
        return array(
            'entities' => $articles,
            'page' => $page,
            'nb_pages' => $nb_pages,
        );
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Displays a form to create a new Article entity.
     *
     * @Route("/new", name="article_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        //  On crée une instance de la classe article
        $article = new Article();
        //  On déclare le formulaire de création de l'entité Article
        $form   = $this->createCreateForm($article);
        //  On gère la soumissoon du formulire
        if( $request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                //  On enregistre l'entité
                $em = $this->getDoctrine()->getManager();
                $article->setUser($this->getUser());
                $em->persist($article);
                $em->flush();
                //  On redirige vers la page de visualisation de l'entité crée
                return $this->redirect($this->generateUrl('article_show', array('id' => $article->getId())));
            }
        }
        //  On affiche la vue
        return array(
            'entity' => $article,
            'form'   => $form->createView(),
        );
    }
    /**
    * Creates a form to create a Article entity.
    *
    * @param Article $article The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Article $article)
    {
        //  On crée un formulaire de crétion de l'entité
        $form = $this->createForm(new ArticleType(), $article, array(
            'action' => $this->generateUrl('article_new'),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  On crée un formulaire pour la soumission du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.create', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Finds and displays a Article entity.
     *
     * @Route("/{id}", name="article_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Article $article)
    {        
        //  On recupère le formulaire de suppression de l'entité
        $deleteForm = $this->createDeleteForm($article->getId());
        //  On affiche la 
        return array(
            'entity'      => $article,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Displays a form to edit an existing Article entity.
     *
     * @Route("/{id}/edit", name="article_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        //  On déclarre les formulaires de d'édition et de suppresion de l'entité Article
        $editForm = $this->createEditForm($article);
        $deleteForm = $this->createDeleteForm($article->getId());
        //  On gère ici la soumission du formulaire d'édition
        if( $request->getMethod() == 'PUT')
        {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                //  On met à jour l'entité
                $em->flush();
                //  On récupère le service translator
                $translator = $this->get('translator');
                //  On affiche un message à l'utilisateur
                $texteTraduit = $translator->trans('message.notice');
                $this->get('session')->getFlashBag()->add('notice', $texteTraduit);                
                // On fait une redirection vers la page de liste de l'entité Article                
                return $this->redirect($this->generateUrl('article'));
            }
        }

        //  On affiche la vue
        return array(
            'entity'      => $article,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Article entity.
    *
    * @param Article $article The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Article $article)
    {
        //  On c rée le formulaire de mise à jour de l'entité
        $form = $this->createForm(new ArticleType(), $article, array(
            'action' => $this->generateUrl('article_edit', array('id' => $article->getId())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  On crée un bouton pour la soumission du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.update', 'attr' => array( 'class' => 'btn btn-primary' )));
        //  On retourne le formulaire
        return $form;
    }
    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Deletes a Article entity.
     *
     * @Route("/{id}", name="article_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Article $article)
    {
        //  On recupère le formulaire de suppression de l'entité
        $form = $this->createDeleteForm($article->getId());
        $form->handleRequest($request);
        //  On vérifie que le formulaire est valide
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //  On supprime l'entité
            $em->remove($article);
            $em->flush();
        }
        // On fait une redirection vers la page de liste de l'entité Article  
        return $this->redirect($this->generateUrl('article'));
    }

    /**
     * Creates a form to delete a Article entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.delete', 'attr' => array( 'class' => 'btn btn-danger' )))
            ->getForm()
        ;
    }
}
