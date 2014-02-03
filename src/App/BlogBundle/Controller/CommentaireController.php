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
use App\BlogBundle\Entity\Commentaire;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Commentaire controller.
 *
 * @Route("/commentaire")
 */
class CommentaireController extends Controller
{

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Lists all Commentaire entities.
     *
     * @Route("/", name="commentaire")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        //  On recupère toutes les entités
        $nombreParPage = $this->container->getParameter('commentaire');         
        $commentaires =   $em->getRepository('AppBlogBundle:Commentaire')->getCommentaires($nombreParPage, $page);
        //  Le nombre de page à afficher
        $nb_pages = (count($commentaires) > 0) ? ceil(count($commentaires)/$nombreParPage) : 1;
        //  On affiche la vue
        return array(
            'entities' => $commentaires,
            'page' => $page,
            'nb_pages' => $nb_pages,
        );
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Finds and displays a Commentaire entity.
     *
     * @Route("/{id}", name="commentaire_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Commentaire $commentaire)
    {        
        //  On recupère le formulaire de suppression de l'entité
        $deleteForm = $this->createDeleteForm($commentaire->getId());
        //  On affiche la 
        return array(
            'entity'      => $commentaire,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * 
     * Displays a form to edit an existing Commentaire entity.
     *
     * @Route("/{id}/edit", name="commentaire_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();
        //  On déclarre les formulaires de d'édition et de suppresion de l'entité Commentaire
        $editForm = $this->createEditForm($commentaire);
        $deleteForm = $this->createDeleteForm($commentaire->getId());
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
                // On fait une redirection vers la page de liste de l'entité Commentaire                
                return $this->redirect($this->generateUrl('commentaire'));
            }
        }
        //  On affiche la vue
        return array(
            'entity'      => $commentaire,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Commentaire entity.
    *
    * @param Commentaire $commentaire The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Commentaire $commentaire)
    {
        //  On c rée le formulaire de mise à jour de l'entité
        $form = $this->createForm('commentaire', $commentaire, array(
            'action' => $this->generateUrl('commentaire_edit', array('id' => $commentaire->getId())),
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
     * Deletes a Commentaire entity.
     *
     * @Route("/{id}", name="commentaire_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Commentaire $commentaire)
    {
        //  On recupère le formulaire de suppression de l'entité
        $form = $this->createDeleteForm($commentaire->getId());
        $form->handleRequest($request);
        //  On vérifie que le formulaire est valide
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //  On supprime l'entité
            $em->remove($commentaire);
            $em->flush();
        }
        // On fait une redirection vers la page de liste de l'entité Commentaire  
        return $this->redirect($this->generateUrl('commentaire'));
    }

    /**
     * Creates a form to delete a Commentaire entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commentaire_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.delete', 'attr' => array( 'class' => 'btn btn-danger' )))
            ->getForm()
        ;
    }
}
