<?php

namespace App\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\BlogBundle\Entity\Commentaire;
use App\BlogBundle\Form\CommentaireType;

/**
 * Commentaire controller.
 *
 * @Route("/commentaire")
 */
class CommentaireController extends Controller
{

    /**
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
        $entities =   $em->getRepository('AppBlogBundle:Commentaire')->getCommentaires($nombreParPage, $page);
        //  Le nombre de page à afficher
        $nb_pages = (count($entities) > 0) ? ceil(count($entities)/$nombreParPage) : 1;

        //  On affiche la vue
        return array(
            'entities' => $entities,
            'page' => $page,
            'nb_pages' => $nb_pages,
        );
    }

    /**
     * Displays a form to create a new Commentaire entity.
     *
     * @Route("/new", name="commentaire_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new Commentaire();
        //  On déclare le formulaire de création de l'entité Commentaire
        $form   = $this->createCreateForm($entity);
        //  On gère la soumissoon du formulire
        if( $request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                //  On enregistre l'entité
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                //  On redirige vers la page de visualisation de l'entité crée
                return $this->redirect($this->generateUrl('commentaire_show', array('id' => $entity->getId())));
            }
        }

        //  On affiche la vue
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    /**
    * Creates a form to create a Commentaire entity.
    *
    * @param Commentaire $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Commentaire $entity)
    {
        //  On crée un formulaire de crétion de l'entité
        $form = $this->createForm(new CommentaireType(), $entity, array(
            'action' => $this->generateUrl('commentaire_new'),
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  On crée un formulaire pour la soumission du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.create', 'attr' => array( 'class' => 'btn' )));
        //  On retourne le formulaire
        return $form;
    }

    /**
     * Finds and displays a Commentaire entity.
     *
     * @Route("/{id}", name="commentaire_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Commentaire $entity)
    {
        
        //  On recupère le formulaire de suppression de l'entité
        $deleteForm = $this->createDeleteForm($entity->getId());

        //  On affiche la 
        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Commentaire entity.
     *
     * @Route("/{id}/edit", name="commentaire_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Request $request, Commentaire $entity)
    {
        $em = $this->getDoctrine()->getManager();
        //  On déclarre les formulaires de d'édition et de suppresion de l'entité Commentaire
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($entity->getId());
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
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Commentaire entity.
    *
    * @param Commentaire $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Commentaire $entity)
    {
        //  On c rée le formulaire de mise à jour de l'entité
        $form = $this->createForm(new CommentaireType(), $entity, array(
            'action' => $this->generateUrl('commentaire_edit', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('class' => 'form-horizontal well'),
        ));
        //  On crée un bouton pour la soumission du formulaire
        $form->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.update', 'attr' => array( 'class' => 'btn' )));
        //  On retourne le formulaire
        return $form;
    }
    /**
     * Deletes a Commentaire entity.
     *
     * @Route("/{id}", name="commentaire_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Commentaire $entity)
    {
        //  On recupère le formulaire de suppression de l'entité
        $form = $this->createDeleteForm($entity->getId());
        $form->handleRequest($request);
        //  On vérifie que le formulaire est valide
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //  On supprime l'entité
            $em->remove($entity);
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
            ->add('submit', 'submit', array('translation_domain' => 'FOSUserBundle', 'label' => 'actions.delete', 'attr' => array( 'class' => 'btn' )))
            ->getForm()
        ;
    }
}
