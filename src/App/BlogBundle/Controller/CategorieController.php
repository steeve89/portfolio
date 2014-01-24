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
use App\BlogBundle\Entity\Categorie;
use App\BlogBundle\Form\CategorieType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Categorie controller.
 *
 * @Route("/categorie")
 */
class CategorieController extends Controller
{

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Lists all Categorie entities.
     *
     * @Route("/", name="categorie")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        //  On recupère toutes les entités
        $nombreParPage = $this->container->getParameter('categorie');         
        $categories =   $em->getRepository('AppBlogBundle:Categorie')->getCategories($nombreParPage, $page);
        //  Le nombre de p$categoriesage à afficher
        $nb_pages = (count($categories) > 0) ? ceil(count($categories)/$nombreParPage) : 1;
        //  On affiche la vue
        return array(
            'entities' => $categories,
            'page' => $page,
            'nb_pages' => $nb_pages,
        );
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Displays a form to create a new Categorie entity.
     *
     * @Route("/new", name="categorie_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        //  On definit une instance de la classe categorie
        $categorie = new Categorie();
        //  On déclare le formulaire de création de l'entité Categorie
        $form   = $this->createCreateForm($categorie);
        //  On gère la soumissoon du formulire
        if( $request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                //  On enregitre la categorie
                $em = $this->getDoctrine()->getManager();
                $em->persist($categorie);
                $em->flush();
                //  On redirige vers la page de visualisation de l'entité crée
                return $this->redirect($this->generateUrl('categorie_show', array('id' => $categorie->getId())));
            }
        }
        //  On affiche la vue
        return array(
            'entity' => $categorie,
            'form'   => $form->createView(),
        );
    }
    /**
    * Creates a form to create a Categorie entity.
    *
    * @param Categorie $categorie The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Categorie $categorie)
    {
        //  On crée un formulaire de crétion de l'entité
        $form = $this->createForm(new CategorieType(), $categorie, array(
            'action' => $this->generateUrl('categorie_new'),
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
     * Finds and displays a Categorie entity.
     *
     * @Route("/{id}", name="categorie_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Categorie $categorie)
    {
        //  On recupère le formulaire de suppression de l'entité
        $deleteForm = $this->createDeleteForm($categorie->getId());
        //  On affiche la vue
        return array(
            'entity'      => $categorie,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Displays a form to edit an existing Categorie entity.
     *
     * @Route("/{id}/edit", name="categorie_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, Categorie $categorie)
    {
        $em = $this->getDoctrine()->getManager();
        //  On déclarre les formulaires de d'édition et de suppresion de l'entité Categorie
        $editForm = $this->createEditForm($categorie);
        $deleteForm = $this->createDeleteForm($categorie->getId());
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
                // On fait une redirection vers la page de liste de l'entité Categorie                
                return $this->redirect($this->generateUrl('categorie'));
            }
        }
        //  On affiche la vue
        return array(
            'entity'      => $categorie,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Categorie entity.
    *
    * @param Categorie $categorie The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Categorie $categorie)
    {
        //  On c rée le formulaire de mise à jour de l'entité
        $form = $this->createForm(new CategorieType(), $categorie, array(
            'action' => $this->generateUrl('categorie_edit', array('id' => $categorie->getId())),
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
     * Deletes a Categorie entity.
     *
     * @Route("/{id}", name="categorie_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Categorie $categorie)
    {
        //  On recupère le formulaire de suppression de l'entité
        $form = $this->createDeleteForm($categorie->getId());
        $form->handleRequest($request);
        //  On vérifie que le formulaire est valide
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //  On vérifie que la catégorie n'a pas d'article
            $articles = $categorie->getArticles();
            if(count($articles) > 0)
            {
                //  On récupère le service translator
                $translator = $this->get('translator');
                //  On affiche un message à l'utilisateur
                $texteTraduit = $translator->trans('categorie.error.delete', array(), 'FOSUserBundle');
                $this->get('session')->getFlashBag()->add('error', $texteTraduit);  
            } else {
                //  On supprime l'entité
                $em->remove($categorie);
                $em->flush();
            }            
        }
        // On fait une redirection vers la page de liste de l'entité Categorie  
        return $this->redirect($this->generateUrl('categorie'));
    }

    /**
     * Creates a form to delete a Categorie entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('categorie_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.delete', 'attr' => array( 'class' => 'btn btn-danger' )))
            ->getForm()
        ;
    }
}
